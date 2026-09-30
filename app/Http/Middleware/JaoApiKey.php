<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JaoApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = config('services.jao.api_key');
        $header = config('services.api.header');
        $allowedOrigins = config('services.jao.allowed_origins', []);

        $apiKey = $request->header($header);
        $origin = $request->header('Origin');

        // Validate API key
        if (
            ! is_string($apiKey) ||
            ! is_string($expectedKey) ||
            ! hash_equals($expectedKey, $apiKey)
        ) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        // Validate Origin only when Origin is actually provided
        if (
            is_string($origin) &&
            ! in_array($origin, $allowedOrigins, true)
        ) {
            return response()->json([
                'message' => 'Origin not allowed.',
            ], 403);
        }

        return $next($request);
    }
}