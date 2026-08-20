<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles)
    {
        $allowedRoles = explode(',', $roles);
        $userRole = $request->input('user_rol');

        if (!in_array($userRole, $allowedRoles)) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'No tiene permisos para esta acción'
            ], 403);
        }

        return $next($request);
    }
}
