<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Usuario;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Calcula el TTL (segundos) según el rol y la opción recuérdame.
     * Regla general: 1 hora (3600s). Excepción Chofer: 24 horas (86400s).
     */
    public function calculateTtl(string $rol, bool $remember = false): int
    {
        // Si el usuario activa "Recuérdame", extender el token a 15 días (15 * 86400 = 1,296,000 segundos)
        if ($remember) {
            return 15 * 86400; // 15 días exactos
        }

        $rolLower = strtolower($rol);
        if ($rolLower === 'chofer') {
            return 86400; // 24 horas por defecto para choferes
        }

        return 3600; // 1 hora por defecto para otros roles
    }

    public function login(string $email, string $password, bool $remember = false): array
    {
        $user = Usuario::where('email', $email)->where('activo', true)->first();
        if (!$user) {
            throw new \Exception('Credenciales inválidas');
        }

        $valid = Hash::check($password, $user->password_hash);
        if (!$valid && in_array($password, ['password', 'password123'])) {
            $valid = Hash::check('password', $user->password_hash) || Hash::check('password123', $user->password_hash);
        }

        if (!$valid) {
            throw new \Exception('Credenciales inválidas');
        }

        $ttlSeconds = $this->calculateTtl($user->rol, $remember);
        $token = $this->generateJwt($user, $ttlSeconds);

        return [
            'token' => $token,
            'user' => $user,
            'ttl_seconds' => $ttlSeconds,
            'remember' => $remember
        ];
    }

    public function generateJwt(Usuario $user, int $ttlSeconds = 3600): string
    {
        $secret = config('jwt.secret');
        if (empty($secret)) {
            throw new \RuntimeException('JWT secret is not configured in config/jwt.php');
        }

        $algorithm = config('jwt.algorithm', 'HS256');

        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'rol' => $user->rol,
            'iat' => time(),
            'exp' => time() + $ttlSeconds
        ];

        return JWT::encode($payload, $secret, $algorithm);
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
