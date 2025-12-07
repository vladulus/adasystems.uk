<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware pentru autentificarea device-urilor ADA-Pi.
 * 
 * Verifică JWT-ul trimis de Pi în header-ul Authorization.
 * Separat de user authentication (tymon/jwt-auth).
 */
class VerifyAdaPiJwt
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        // 1. Verifică că există header
        if (!$authHeader) {
            return response()->json([
                'status' => 'error',
                'message' => 'Missing Authorization header'
            ], 401);
        }

        // 2. Verifică formatul "Bearer <token>"
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Authorization format. Expected: Bearer <token>'
            ], 401);
        }

        $token = substr($authHeader, 7); // Remove "Bearer "

        // 3. Decodează și verifică JWT
        try {
            $secret = config('ada-pi.jwt_secret');
            
            if (empty($secret)) {
                \Log::error('ADA-Pi JWT secret not configured');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Server configuration error'
                ], 500);
            }

            $decoded = $this->decodeJwt($token, $secret);

            if (!$decoded) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token'
                ], 401);
            }

            // 4. Verifică expirare
            if (isset($decoded['exp']) && $decoded['exp'] < time()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token expired'
                ], 401);
            }

            // 5. Verifică că device_id din JWT match-uie cu cel din request
            $jwtDeviceId = $decoded['device'] ?? null;
            $requestDeviceId = $request->input('device_id');

            if ($jwtDeviceId && $requestDeviceId && $jwtDeviceId !== $requestDeviceId) {
                \Log::warning("ADA-Pi JWT device mismatch: token={$jwtDeviceId}, request={$requestDeviceId}");
                return response()->json([
                    'status' => 'error',
                    'message' => 'Device ID mismatch'
                ], 403);
            }

            // 6. Adaugă device_id la request pentru controller
            $request->merge(['verified_device_id' => $jwtDeviceId]);

        } catch (\Exception $e) {
            \Log::warning('ADA-Pi JWT validation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token'
            ], 401);
        }

        return $next($request);
    }

    /**
     * Decode JWT token manually (HS256 only)
     */
    private function decodeJwt(string $token, string $secret): ?array
    {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            return null;
        }

        [$header64, $payload64, $signature64] = $parts;

        // Verify signature
        $expectedSignature = $this->base64UrlEncode(
            hash_hmac('sha256', "$header64.$payload64", $secret, true)
        );

        if (!hash_equals($expectedSignature, $signature64)) {
            \Log::warning('ADA-Pi JWT signature mismatch');
            return null;
        }

        // Decode payload
        $payload = json_decode($this->base64UrlDecode($payload64), true);

        return $payload;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}