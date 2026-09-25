<?php

namespace App\Http\Middleware;

use App\Models\ApplicationInvitation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApplicationInvitation
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->route('token');
        if (!$token) {
            return response()->json(['message' => 'Invalid token']);
        }
        $isValid =  ApplicationInvitation::where('token', $token)
            ->whereNull('used_at')
            ->where('expired_at', '>', now())->exists();
        if (!$isValid) {
            return response()->json(['message' => 'Invalid or expired invitation']);
        }
        return $next($request);
    }
}
