<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Usuario;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function login(string $email, string $password): array
    {
        $user = Usuario::where('email', $email)->where('activo', true)->first();
        if (!$user || !Hash::check($password, $user->password_hash)) {
            throw new \Exception('Credenciales inválidas');
        }

        return [
            'token' => $this->generateJwt($user),
            'user' => $user
        ];
    }

    public function generateJwt(Usuario $user): string
    {
        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'rol' => $user->rol,
            'iat' => time(),
            'exp' => time() + config('jwt.ttl', 3600)
        ];
        return JWT::encode($payload, env('JWT_SECRET'), 'HS256');
    }

    public function generatePin(int $digits = 6): string
    {
        return str_pad((string) rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
    }

    public function sendRecoveryEmail(Usuario $user, string $pin): void
    {
        // ...
    }

    public function resetPassword(string $email, string $pin, string $newPassword): bool
    {
        return true;
    }
}
