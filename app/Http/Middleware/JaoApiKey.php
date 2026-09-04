<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JaoApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = config('services.jao.api_key');
        $header = config('services.api.header');
        $allowedOrigins = config('services.jao.allowed_origins', []);

        $apiKey = $request->header($header);
        $origin = $request->header('Origin');

        if (
            !is_string($apiKey) ||
            !is_string($expectedKey) ||
            !hash_equals($expectedKey, $apiKey)
        ) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        if (
            !is_string($origin) ||
            !in_array($origin, $allowedOrigins, true)
        ) {
            return response()->json([
                'message' => 'Origin not allowed.',
            ], 403);
        }


        if (!$apiKey || !hash_equals($expectedKey, $apiKey)) {
            return response()->json(['message' => 'Invalid Key.'], 401);
        }

        return $next($request);
    }
}
