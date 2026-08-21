<?php declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?Usuario
    {
        return Usuario::where('email', $email)->first();
    }

    public function findById(int $id): ?Usuario
    {
        return Usuario::find($id);
    }

    public function getAll(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Usuario::query();

        if (isset($filters['roles']) && is_array($filters['roles'])) {
            $query->whereIn('rol', $filters['roles']);
        }

        return $query->get();
    }

    public function create(array $data): Usuario
    {
        $data['password_hash'] = Hash::make($data['password']);
        unset($data['password']);
        return Usuario::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return (bool) Usuario::where('id', $id)->update($data);
    }

    public function deactivate(int $id): bool
    {
        return (bool) Usuario::where('id', $id)->update(['activo' => false]);
    }

    public function storePinRecovery(int $id, string $pinHash): void
    {
        Usuario::where('id', $id)->update([
            'recovery_pin_hash' => $pinHash,
            'recovery_pin_expires_at' => now()->addMinutes(15)
        ]);
    }

    public function findByPinRecovery(string $email): ?Usuario
    {
        return Usuario::where('email', $email)
            ->where('recovery_pin_expires_at', '>', now())
            ->first();
    }
}
