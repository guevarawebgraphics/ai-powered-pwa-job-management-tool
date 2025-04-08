<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EnsureTokenIsNotExpired
{
    public function handle(Request $request, Closure $next)
    {
        // Get the current access token from the user context
        $token = $request->user()?->currentAccessToken();

        // If there's a token and an expires_at set, check if it is expired
        if ($token && $token->expires_at && Carbon::now()->greaterThan($token->expires_at)) {
            // Optionally delete the token so it can’t be reused
            $token->delete();

            return response()->json([
                'message' => 'Token expired. Please log in again.'
            ], 401);
        }

        return $next($request);
    }
}
