<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Token\StoreRequest;
use App\Http\Requests\Api\Token\DestroyRequest;
use App\Models\Api\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TokenController extends Controller
{
    /**
     * GET /auth/tokens
     * Saját tokenek listázása.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $tokens = $user?->tokens()->select(['id','name','last_used_at','created_at'])->get();

        return response()->json([
            'success' => true,
            'data'    => [
                'tokens' => $tokens,
            ],
            'message' => 'Tokens listed.',
        ]);
    }

    /**
     * POST /auth/token
     * Bejelentkezés + új token létrehozása.
     */
    public function store(StoreRequest $request)
    {
        $credentials = $request->only(['email','password']);
        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'data'    => (object)[],
                'message' => 'Invalid credentials.',
            ], 422);
        }

        $deviceName = $request->input('device_name') ?: 'api';
        $token = $user->createToken($deviceName);

        return response()->json([
            'success' => true,
            'data'    => [
                'token' => $token->plainTextToken,
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
            ],
            'message' => 'Login successful.',
        ]);
    }

    /**
     * GET /auth/tokens/{id}
     * Egy saját token metaadatainak lekérése.
     */
    public function show(Request $request, string $id)
    {
        $user = $request->user();
        $token = $user->tokens()->where('id', $id)->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'data'    => (object)[],
                'message' => 'Token not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'token' => [
                    'id'          => $token->id,
                    'name'        => $token->name,
                    'last_used_at'=> $token->last_used_at,
                    'created_at'  => $token->created_at,
                ],
            ],
            'message' => 'Token details.',
        ]);
    }

    /**
     * PUT /auth/tokens/{id}
     * Token átnevezése.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required','string','max:255'],
        ]);

        $user = $request->user();
        $token = $user->tokens()->where('id', $id)->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'data'    => (object)[],
                'message' => 'Token not found.',
            ], 404);
        }

        $token->name = (string)$request->input('name');
        $token->save();

        return response()->json([
            'success' => true,
            'data'    => [
                'token' => [
                    'id'          => $token->id,
                    'name'        => $token->name,
                    'last_used_at'=> $token->last_used_at,
                    'created_at'  => $token->created_at,
                ],
            ],
            'message' => 'Token updated.',
        ]);
    }

    /**
     * DELETE /auth/token
     * Kijelentkezés: az aktuális Bearer token törlése.
     */
    public function destroy(DestroyRequest $request)
    {
        $token = $request->user()?->currentAccessToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'data'    => (object)[],
                'message' => 'No active token.',
            ], 400);
        }

        $token->delete();

        return response()->json([
            'success' => true,
            'data'    => (object)[],
            'message' => 'Logged out.',
        ]);
    }
}
