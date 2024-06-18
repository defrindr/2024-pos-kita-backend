<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use JWTAuth;

class UmkmMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Attempt to authenticate the user using the JWT token
            $user = JWTAuth::parseToken()->authenticate();

            if ($user->id_role == "1" || $user->id_role == "1") {
                return $next($request);
            }

            return response()->json(['error' => 'Unauthorized'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
