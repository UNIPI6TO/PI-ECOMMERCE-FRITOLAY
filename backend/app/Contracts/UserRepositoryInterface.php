<?php declare(strict_types=1);

namespace App\Contracts;
use App\Models\Usuario;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?Usuario;
    public function findById(int $id): ?Usuario;
    public function getAll(array $filters = []): \Illuminate\Database\Eloquent\Collection;
    public function create(array $data): Usuario;
    public function update(int $id, array $data): bool;
    public function deactivate(int $id): bool;
    public function storePinRecovery(int $id, string $pinHash): void;
    public function findByPinRecovery(string $email): ?Usuario;
}
