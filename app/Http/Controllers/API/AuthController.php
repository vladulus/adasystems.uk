<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Login endpoint for ADA-Pi dashboard
     * POST /api/auth/login
     */
    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'username'  => 'required|string',
        'password'  => 'required|string',
        'device_id' => 'nullable|string', // Optional: care Pi se loghează
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors'  => $validator->errors(),
        ], 422);
    }

    // Căutăm user după email sau name
    $user = User::where('email', $request->username)
        ->orWhere('name', $request->username)
        ->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    // Dacă ai coloana is_active pe users
    if (isset($user->is_active) && !$user->is_active) {
        return response()->json([
            'success' => false,
            'message' => 'Account is disabled',
        ], 403);
    }

    // Update last_login + device_id (dacă există coloanele)
    $user->update([
        'last_login' => now(),
        'device_id'  => $request->device_id ?? $user->device_id,
    ]);

    // Generăm JWT
    $token = JWTAuth::fromUser($user);

    // Permisiuni + rol
    $permissions = $user->getAllPermissions()->pluck('name');
    $role        = $user->roles->first()?->name ?? 'user';

    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'data'    => [
            'token'      => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60, // secunde
            'user'       => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'role'        => $role,
                'permissions' => $permissions,
                'parent_id'   => $user->parent_id ?? null,
                'device_id'   => $user->device_id ?? null,
            ],
        ],
    ], 200);
}


    /**
     * Get authenticated user details
     * GET /api/auth/me
     */
    public function me()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'status' => 'ok',
            'data'   => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'role'        => $user->roles->first()?->name ?? 'user',
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'parent_id'   => $user->parent_id ?? null,
                'device_id'   => $user->device_id ?? null,
                'last_login'  => $user->last_login ?? null,
            ],
        ]);
    }

    /**
     * Refresh JWT token
     * POST /api/auth/refresh
     */
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return response()->json([
                'status' => 'ok',
                'data'   => [
                    'token'      => $newToken,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token refresh failed',
            ], 401);
        }
    }

    /**
     * Logout
     * POST /api/auth/logout
     */
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status'  => 'ok',
                'message' => 'Successfully logged out',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Logout failed',
            ], 500);
        }
    }

    /**
     * Validate JWT token
     * POST /api/auth/validate
     */
    public function validateToken(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }

            return response()->json([
                'status' => 'ok',
                'data'   => [
                    'user_id' => $user->id,
                    'role'    => $user->roles->first()?->name ?? 'user',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token validation failed',
            ], 401);
        }
    }
}
