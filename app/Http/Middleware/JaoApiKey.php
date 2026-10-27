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
        $header = config('services.api.header') ?? 'X-API-Key';

        if (! is_string($header) || trim($header) === '') {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $apiKey = $request->header($header);

        if (
            ! is_string($apiKey) ||
            ! is_string($expectedKey) ||
            trim($expectedKey) === '' ||
            ! hash_equals($expectedKey, $apiKey)
        ) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        return $next($request);
    }
}
