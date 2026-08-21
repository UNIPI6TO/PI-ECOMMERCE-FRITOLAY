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
        $user = \App\Models\Usuario::find($request->input('user_id'));
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        return response()->json($user);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $request->input('user_id')
        ]);
        
        $user = \App\Models\Usuario::find($request->input('user_id'));
        $user->update([
            'nombre' => $request->nombre,
            'email' => $request->email
        ]);
        
        return response()->json(['message' => 'Perfil actualizado correctamente', 'user' => $user]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        $user = \App\Models\Usuario::find($request->input('user_id'));
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password_hash)) {
            return response()->json(['error' => 'La contraseña actual es incorrecta'], 400);
        }

        $user->update(['password_hash' => \Illuminate\Support\Facades\Hash::make($request->new_password)]);
        
        return response()->json(['message' => 'Contraseña actualizada correctamente']);
    }
}
