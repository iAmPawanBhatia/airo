<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateQuotationJwt
{
    /**
     * Handle an incoming request and validate JWT token.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('services.quotation.jwt_secret');
        if (! is_string($secret) || $secret === '') {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'QUOTATION_JWT_SECRET is not configured.');
        }

        $header = $request->header('Authorization', '');
        if (! str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = trim(substr($header, 7));

        try {
            $key = new Key($secret, 'HS256');
            JWT::decode($token, $key);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
