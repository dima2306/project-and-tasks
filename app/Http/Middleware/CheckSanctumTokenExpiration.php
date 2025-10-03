<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSanctumTokenExpiration
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check if user is authenticated via Sanctum
        if ($request->user() && $request->user()->currentAccessToken()) {
            $token = $request->user()->currentAccessToken();

            // Check if token is expired using our custom method
            if ($token->isExpired()) {
                // Delete the expired token
                $token->delete();

                return response()->json([
                    'message' => 'Token has expired.'
                ], 401);
            }
        }

        return $next($request);
    }
}
