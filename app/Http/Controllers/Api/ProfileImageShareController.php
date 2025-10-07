<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProfileImageShare\StoreRequest;
use App\Http\Requests\Api\ProfileImageShare\UpdateRequest;
use App\Models\Api\ProfileImage;
use App\Models\Api\ProfileImageShare;
use Illuminate\Http\Request;

class ProfileImageShareController extends Controller
{
    // GET /api/v1/profile-image-shares?profile_image_id=ID
    public function index(Request $request)
    {
        $userId = optional($request->user())->id;
        $query = ProfileImageShare::query()->with(['image.profile:id,user_id', 'user:id,name,email']);

        if ($request->filled('profile_image_id')) {
            $query->where('profile_image_id', (int)$request->query('profile_image_id'));
        }

        $shares = $query->get()->filter(function ($share) use ($userId) {
            // csak a kép tulajdonosa láthatja a megosztásait
            return optional(optional($share->image)->profile)->user_id === $userId;
        })->values();

        return response()->json([
            'success' => true,
            'data'    => $shares,
            'message' => 'Profile image shares list.',
        ]);
    }

    // POST /api/v1/profile-image-shares
    public function store(StoreRequest $request)
    {
        $img = ProfileImage::with('profile:id,user_id')->findOrFail((int)$request->input('profile_image_id'));
        $this->authorizeOwner($request, $img);

        $share = ProfileImageShare::firstOrCreate([
            'profile_image_id'    => $img->id,
            'shared_with_user_id' => (int)$request->input('shared_with_user_id'),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $share->load('user:id,name,email'),
            'message' => 'Profile image shared.',
        ], 201);
    }

    // GET /api/v1/profile-image-shares/{id}
    public function show(Request $request, ProfileImageShare $profileImageShare)
    {
        $img = $profileImageShare->load('image.profile:id,user_id')->image;
        $this->authorizeOwner($request, $img);

        return response()->json([
            'success' => true,
            'data'    => $profileImageShare->load('user:id,name,email'),
            'message' => 'Profile image share details.',
        ]);
    }

    // PUT /api/v1/profile-image-shares/{id}
    public function update(UpdateRequest $request, ProfileImageShare $profileImageShare)
    {
        $img = $profileImageShare->load('image.profile:id,user_id')->image;
        $this->authorizeOwner($request, $img);

        $profileImageShare->update($request->validated());

        return response()->json([
            'success' => true,
            'data'    => $profileImageShare->load('user:id,name,email'),
            'message' => 'Profile image share updated.',
        ]);
    }

    // DELETE /api/v1/profile-image-shares/{id}
    public function destroy(Request $request, ProfileImageShare $profileImageShare)
    {
        $img = $profileImageShare->load('image.profile:id,user_id')->image;
        $this->authorizeOwner($request, $img);

        $profileImageShare->delete();

        return response()->json([
            'success' => true,
            'data'    => [],
            'message' => 'Profile image share revoked.',
        ]);
    }

    private function authorizeOwner(Request $request, ?ProfileImage $image): void
    {
        $userId = optional($request->user())->id;
        $ownerId = optional(optional($image)->profile)->user_id;

        abort_if(!$userId || !$ownerId || (int)$userId !== (int)$ownerId, 403, 'Only the owner can manage shares.');
    }
}
