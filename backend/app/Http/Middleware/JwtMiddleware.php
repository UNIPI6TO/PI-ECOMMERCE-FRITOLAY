<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Illuminate\Support\Facades\Cookie;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Buscar token en Header Authorization: Bearer <token>
        $token = $request->bearerToken();

        // 2. Fallback: buscar token en Cookie HttpOnly 'jwt_token'
        if (!$token) {
            $token = $request->cookie('jwt_token');
        }

        if (!$token) {
            return response()->json(['error' => 'Unauthorized', 'message' => 'Sesión no iniciada o no autorizada.'], 401);
        }

        try {
            $secret = config('jwt.secret');
            if (empty($secret)) {
                return response()->json(['error' => 'Server misconfiguration', 'message' => 'JWT secret is missing'], 500);
            }

            $algorithm = config('jwt.algorithm', 'HS256');
            $decoded = JWT::decode($token, new Key($secret, $algorithm));
            
            $request->merge([
                'user_id' => $decoded->sub,
                'user_rol' => $decoded->rol
            ]);
        } catch (Exception $e) {
            $forgetCookie = Cookie::forget('jwt_token');
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Sesión expirada o token inválido.'
            ], 401)->withCookie($forgetCookie);
        }

        return $next($request);
    }
}
