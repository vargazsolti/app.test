<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DatingProfile\StoreRequest;
use App\Http\Requests\Api\DatingProfile\UpdateRequest;
use App\Models\Api\DatingProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DatingProfileController extends Controller
{
    // GET /api/v1/dating-profiles
    public function index(Request $request)
    {
        $query = DatingProfile::query()->with(['user:id,name,email', 'languages:id,code,name']);

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->get('city') . '%');
        }
        if ($request->filled('country')) {
            $query->where('country', $request->get('country'));
        }
        if ($request->filled('body_type')) {
            $query->where('body_type', $request->get('body_type'));
        }

        $profiles = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $profiles,
            'message' => 'Dating profiles listed successfully.',
        ]);
    }

    // POST /api/v1/dating-profiles
    public function store(StoreRequest $request)
    {
        $authUser = $request->user();

        // Admin létrehozhat más userhez is profilt: opcionális user_id
        $targetUserId = $authUser->id;
        if ($authUser->is_admin && $request->filled('user_id')) {
            // minimális inline validáció az opcionális user_id-re
            $request->validate([
                'user_id' => ['required', 'integer', 'exists:users,id'],
            ]);
            $targetUserId = (int) $request->input('user_id');
        }

        // Egy userhez csak 1 profil
        if (DatingProfile::where('user_id', $targetUserId)->exists()) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'A felhasználóhoz már létezik társkereső profil.',
            ], 422);
        }

        $payload = $request->validated();
        $languageIds = $payload['language_ids'] ?? [];
        unset($payload['language_ids'], $payload['user_id']); // ne írjuk felül

        $payload['user_id'] = $targetUserId;

        $profile = DatingProfile::create($payload);
        if (!empty($languageIds)) {
            $profile->languages()->sync($languageIds);
        }

        $profile->load(['user:id,name,email', 'languages:id,code,name']);

        return response()->json([
            'success' => true,
            'data' => $profile,
            'message' => 'Dating profile created successfully.',
        ], 201);
    }

    // GET /api/v1/dating-profiles/{id}?with=images
// GET /api/v1/dating-profiles/{id}?with=images
public function show(Request $request, $id)
{
    $with = collect(explode(',', (string) $request->query('with')))
        ->map(fn ($v) => trim($v))
        ->filter()
        ->values();

    // alap kapcsolatok
    $profile = \App\Models\Api\DatingProfile::with(['user:id,name,email', 'languages:id,code,name'])
        ->find($id);

    if (!$profile) {
        return response()->json([
            'success' => false,
            'data'    => [],
            'message' => 'Dating profile not found.',
        ], 404);
    }

    // ha képeket is kérünk, töltsük be a shares-t is, hogy tudjunk redaktálni
    if ($with->contains('images')) {
        $profile->load([
            'images' => function ($q) {
                $q->orderByDesc('is_primary')
                  ->orderBy('sort_order')
                  ->orderByDesc('id');
            },
            'images.shares:profile_image_id,shared_with_user_id',
        ]);

        $viewerId    = optional($request->user())->id;
        $ownerId     = (int) $profile->user_id;
        $placeholder = asset('img/locked-placeholder.png');

        // redaktálás: aki nem tulaj / nincs megosztva, az privát képnél placeholdert kap
        $profile->images->transform(function ($img) use ($viewerId, $ownerId, $placeholder,$request) {
           $isOwner  = $viewerId && ((int)$viewerId === $ownerId);
$isShared = $img->shares->contains(fn ($s) => (int)$s->shared_with_user_id === (int)$viewerId);
$isAdmin  = (bool) optional($request->user())->is_admin; // <-- admin bypass

$visible = ($img->visibility === 'public') || $isOwner || $isShared || $isAdmin;
            $img->is_redacted = !$visible && ($img->visibility === 'private');

            if ($img->is_redacted) {
                $img->url     = $placeholder;   // <<< itt cserélünk placeholderre
                $img->caption = null;           // feliratot rejtsük
            }

            unset($img->shares); // nem kell a kliensnek
            return $img;
        });
    }

    return response()->json([
        'success' => true,
        'data'    => $profile,
        'message' => 'Dating profile retrieved successfully.',
    ]);
}


    // PUT /api/v1/dating-profiles/{id}
    public function update(UpdateRequest $request, $id)
    {
        $profile = DatingProfile::find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Dating profile not found.',
            ], 404);
        }

        $authUser = $request->user();
        if ($profile->user_id !== $authUser->id && !$authUser->is_admin) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Nincs jogosultság a profil frissítéséhez.',
            ], 403);
        }

        $payload = $request->validated();
        $languageIds = $payload['language_ids'] ?? null;
        unset($payload['language_ids'], $payload['user_id']); // user_id nem módosítható itt

        $profile->update($payload);

        if (is_array($languageIds)) {
            $profile->languages()->sync($languageIds);
        }

        $profile->load(['user:id,name,email', 'languages:id,code,name']);

        return response()->json([
            'success' => true,
            'data' => $profile,
            'message' => 'Dating profile updated successfully.',
        ]);
    }

    // DELETE /api/v1/dating-profiles/{id}
    public function destroy(Request $request, $id)
    {
        $profile = DatingProfile::find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Dating profile not found.',
            ], 404);
        }

        $authUser = $request->user();
        if ($profile->user_id !== $authUser->id && !$authUser->is_admin) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Nincs jogosultság a profil törléséhez.',
            ], 403);
        }

        $profile->delete();

        return response()->json([
            'success' => true,
            'data' => [],
            'message' => 'Dating profile deleted successfully.',
        ]);
    }
}
