<?php

namespace App\Modules\Core\Http\Middlewares;

use Closure;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Http\Request;

class CustomAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        dd('teste');
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['error' => 'Token missing'], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        try {
            $user = $accessToken->tokenable_type::find($accessToken->tokenable_id);
            
            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            auth()->setUser($user);
            
            return $next($request);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}