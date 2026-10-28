<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class VerifyHmacSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-Signature');
        $timestamp = $request->header('X-Timestamp');
        $nonce = $request->header('X-Nonce');
        $secret = config('services.jao.hmac_secret');

        if (
            ! is_string($signature) || strlen($signature) !== 64 || ! ctype_xdigit($signature) ||
            ! is_string($timestamp) || ! ctype_digit($timestamp) ||
            ! is_string($nonce) || ! Str::isUuid($nonce) ||
            ! is_string($secret) || trim($secret) === ''
        ) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $now = now()->getTimestamp();

        if (abs($now - (int) $timestamp) > 300) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $payload = implode("\n", [
            $request->method(),
            $request->getRequestUri(),
            $timestamp,
            $nonce,
            $request->getContent(),
        ]);

        if (! hash_equals(hash_hmac('sha256', $payload, $secret), $signature)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        if (! Cache::add('jao:hmac:'.hash('sha256', $nonce), true, (int) $timestamp + 301 - $now)) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
