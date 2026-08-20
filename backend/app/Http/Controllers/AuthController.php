<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RecoverPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->login($request->email, $request->password);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Logout exitoso']);
    }

    public function recover(RecoverPasswordRequest $request): JsonResponse
    {
        return response()->json(['message' => 'PIN enviado']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Password restablecido']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user_id' => $request->input('user_id'),
            'rol' => $request->input('user_rol')
        ]);
    }
}
