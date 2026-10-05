<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
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


        if (!$signature || $timestamp) {
            return response()->json(['message' => 'Missing important credentials'], 401);
        }

        if (abs(Carbon::now()->getTimestamp() - (int) $timestamp) > 300) {
            return response()->json(['message' => 'Missing important credentials'], 401);
        }

        $method = strtoupper($request->method());

        $path = '/' . ltrim($request->path(), '/');

        $body = $request->getContent();

        $payload = $method . $path . $timestamp . $body;

        $expectedSignature = hash_hmac(
            'sha256',
            $payload,
            config('services.jao.hmac_secret')
        );

        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json(['message' => 'Missing important credentials'], 401);
        }


        return $next($request);
    }
}
