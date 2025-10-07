<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProfileImage\StoreRequest;
use App\Http\Requests\Api\ProfileImage\UpdateRequest;
use App\Models\Api\ProfileImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileImageController extends Controller
{
    /**
     * GET /api/v1/profile-images?dating_profile_id=123&per_page=20
     * Publikus/privát képek listázása; privát képnél nem jogosult néző placeholdert kap.
     */
    public function index(Request $request)
    {
        $viewerId = optional($request->user())->id;

        $query = ProfileImage::query()
            ->with([
                'profile:id,user_id',
                // csak belső döntéshez kell; a válaszból eltávolítjuk
                'shares:profile_image_id,shared_with_user_id'
            ]);

        if ($request->filled('dating_profile_id')) {
            $query->where('dating_profile_id', (int) $request->get('dating_profile_id'));
        }

        $query->orderByRaw('is_primary DESC NULLS LAST')
              ->orderBy('sort_order')
              ->orderByDesc('id');

        $images = $query->paginate($request->integer('per_page', 20));

        $placeholder = asset('img/locked-placeholder.png');

        $images->getCollection()->transform(function (ProfileImage $img) use ($viewerId, $placeholder,$request) {
            $ownerId  = optional($img->profile)->user_id;
$isOwner  = $viewerId && $ownerId && ((int)$viewerId === (int)$ownerId);
$isShared = $img->shares->contains(fn ($s) => (int)$s->shared_with_user_id === (int)$viewerId);
$isAdmin  = (bool) optional($request->user())->is_admin; // <-- admin bypass

$visible = ($img->visibility === 'public') || $isOwner || $isShared || $isAdmin;
            $img->is_redacted = !$visible && ($img->visibility === 'private');

            if ($img->is_redacted) {
                // mutatjuk, hogy van fotó, de a tényleges tartalmat rejtjük
                $img->url     = $placeholder;
                $img->caption = null;
            }

            unset($img->shares);
            return $img;
        });

        return response()->json([
            'success' => true,
            'data'    => $images,
            'message' => 'Profile images list.',
        ]);
    }

    /**
     * POST /api/v1/profile-images
     * multipart/form-data: dating_profile_id, image(file), caption?, visibility? (public|private), is_primary?, sort_order?
     */
    public function store(StoreRequest $request)
{
    $user = $request->user();

    // 🔒 Tulajdon ellenőrzés
    $profile = \App\Models\Api\DatingProfile::with('user:id')
        ->findOrFail((int) $request->input('dating_profile_id'));

    $isOwner = (int) $user->id === (int) $profile->user_id;
    $isAdmin = (bool) ($user->is_admin ?? false);

    if (! $isOwner && ! $isAdmin) {
        return response()->json([
            'success' => false,
            'data'    => [],
            'message' => 'You are not allowed to upload images for this profile.',
        ], 403);
    }

    // ✅ ha idáig eljut, tulaj vagy admin

    $path = $request->file('image')->store('profile-images', 'public');

    $payload = [
        'dating_profile_id' => (int) $request->input('dating_profile_id'),
        'path'              => $path,
        'caption'           => $request->input('caption'),
        'visibility'        => $request->input('visibility', 'public'),
        'is_primary'        => (bool) $request->boolean('is_primary'),
        'sort_order'        => $request->input('sort_order'),
    ];

    $image = \DB::transaction(function () use ($payload) {
        $created = \App\Models\Api\ProfileImage::create($payload);

        if ($created->is_primary) {
            \App\Models\Api\ProfileImage::where('dating_profile_id', $created->dating_profile_id)
                ->where('id', '<>', $created->id)
                ->update(['is_primary' => false]);
        }

        return $created->load('profile');
    });

    return response()->json([
        'success' => true,
        'data'    => $image,
        'message' => 'Profile image uploaded successfully.',
    ], 201);
}


    /**
     * GET /api/v1/profile-images/{profile_image}
     * (Ha publikus/owner/shared, akkor valódi kép; különben placeholder.)
     */
    public function show(Request $request, ProfileImage $profileImage)
    {
        $profileImage->load(['profile:id,user_id', 'shares:profile_image_id,shared_with_user_id']);
        $viewerId   = optional($request->user())->id;
        $ownerId  = optional($img->profile)->user_id;
$isOwner  = $viewerId && $ownerId && ((int)$viewerId === (int)$ownerId);
$isShared = $img->shares->contains(fn ($s) => (int)$s->shared_with_user_id === (int)$viewerId);
$isAdmin  = (bool) optional($request->user())->is_admin; // <-- admin bypass

$visible = ($img->visibility === 'public') || $isOwner || $isShared || $isAdmin;

        $profileImage->is_redacted = !$visible && ($profileImage->visibility === 'private');
        if ($profileImage->is_redacted) {
            $profileImage->url     = asset('img/locked-placeholder.png');
            $profileImage->caption = null;
        }
        unset($profileImage->shares);

        return response()->json([
            'success' => true,
            'data'    => $profileImage,
            'message' => 'Profile image details.',
        ]);
    }

    /**
     * PUT /api/v1/profile-images/{profile_image}
     * Bármely mező frissíthető (file cseréje is); primary állítás kizárólagossággal.
     */
    public function update(UpdateRequest $request, ProfileImage $profileImage)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $profileImage, $request) {
            if ($request->hasFile('image')) {
                if ($profileImage->path && Storage::disk('public')->exists($profileImage->path)) {
                    Storage::disk('public')->delete($profileImage->path);
                }
                $data['path'] = $request->file('image')->store('profile-images', 'public');
            }

            $profileImage->update($data);

            if (array_key_exists('is_primary', $data) && $profileImage->is_primary) {
                ProfileImage::where('dating_profile_id', $profileImage->dating_profile_id)
                    ->where('id', '<>', $profileImage->id)
                    ->update(['is_primary' => false]);
            }
        });

        return response()->json([
            'success' => true,
            'data'    => $profileImage->fresh('profile:id,user_id'),
            'message' => 'Profile image updated.',
        ]);
    }

    /**
     * DELETE /api/v1/profile-images/{profile_image}
     * Törli a DB rekordot és a fizikai fájlt a public disk-ről.
     */
    public function destroy(ProfileImage $profileImage)
    {
        if ($profileImage->path && Storage::disk('public')->exists($profileImage->path)) {
            Storage::disk('public')->delete($profileImage->path);
        }

        $profileImage->delete();

        return response()->json([
            'success' => true,
            'data'    => [],
            'message' => 'Profile image deleted.',
        ]);
    }
}
