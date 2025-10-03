<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
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
        // Get the bearer token from the Authorization header
        $bearerToken = $request->bearerToken();

        if ($bearerToken) {
            // Find the token in the database
            $tokenHash = hash('sha256', $bearerToken);
            $accessToken = PersonalAccessToken::where('token', $tokenHash)->first();

            if ($accessToken && $accessToken->isExpired()) {
                $accessToken->delete();

                return response()->json([
                    'message' => 'Token has expired.',
                ], 401);
            }
        }

        // If we have an authenticated user via Sanctum, also check their current token
        $response = $next($request);

        // After authentication, check if the user has an expired token
        if ($request->user() && $request->user()->currentAccessToken()) {
            $currentToken = $request->user()->currentAccessToken();

            // Find our custom model instance by ID to ensure we have the isExpired method
            $token = PersonalAccessToken::find($currentToken->id);

            if ($token && $token->isExpired()) {
                $token->delete();

                return response()->json([
                    'message' => 'Token has expired.',
                ], 401);
            }
        }

        return $response;
    }
}
