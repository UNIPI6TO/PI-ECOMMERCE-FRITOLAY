<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RecoverPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $email = (string) $request->input('email');
            $password = (string) $request->input('password');
            $remember = (bool) $request->input('remember', false);

            $data = $this->authService->login($email, $password, $remember);
            
            $ttlMinutes = (int) ceil($data['ttl_seconds'] / 60);

            // Cookie segura HttpOnly + SameSite Strict
            $cookie = cookie(
                'jwt_token',
                $data['token'],
                $ttlMinutes,
                '/',
                null,
                config('app.env') === 'production', // Secure en producción (HTTPS)
                true,   // HttpOnly
                false,  // Raw
                'Strict' // SameSite
            );

            return response()->json([
                'token' => $data['token'],
                'user' => $data['user'],
                'expires_in' => $data['ttl_seconds'],
                'remember' => $remember
            ])->withCookie($cookie);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }

    public function registro(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'ruc_cedula' => 'required|string|max:20',
            'razon_social' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:255'
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $usuario = \App\Models\Usuario::create([
                'nombre' => $validated['nombre'],
                'email' => $validated['email'],
                'password_hash' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                'rol' => 'cliente',
                'activo' => true
            ]);

            $cliente = \App\Models\Cliente::create([
                'usuario_id' => $usuario->id,
                'ruc_cedula' => $validated['ruc_cedula'],
                'razon_social' => $validated['razon_social'] ?? $validated['nombre'],
                'telefono' => $validated['telefono'],
                'nombre_cliente' => $validated['nombre']
            ]);

            \App\Models\DireccionCliente::create([
                'cliente_id' => $cliente->id,
                'descripcion' => $validated['direccion'],
                'latitud' => 0.0,
                'longitud' => 0.0,
                'es_por_defecto' => true,
                'estado' => true
            ]);

            \Illuminate\Support\Facades\DB::commit();

            // Auto-login
            $data = $this->authService->login($validated['email'], $validated['password'], false);
            $ttlMinutes = (int) ceil($data['ttl_seconds'] / 60);

            $cookie = cookie(
                'jwt_token',
                $data['token'],
                $ttlMinutes,
                '/',
                null,
                config('app.env') === 'production',
                true,
                false,
                'Strict'
            );

            return response()->json([
                'token' => $data['token'],
                'user' => $data['user'],
                'expires_in' => $data['ttl_seconds']
            ], 201)->withCookie($cookie);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['error' => 'Error al registrar cliente: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $forgetCookie = Cookie::forget('jwt_token');
        return response()->json(['message' => 'Sesión cerrada exitosamente'])->withCookie($forgetCookie);
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
