<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Generate a JWT token for API authentication.
     *
     * @return JsonResponse
     */
    public function token(): JsonResponse
    {
        $key = (string) config('services.quotation.jwt_secret');
        if ($key === '') {
            return response()->json([
                'message' => 'QUOTATION_JWT_SECRET is not configured.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $payload = [
            'sub' => 'quotation',
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]);
    }
}


