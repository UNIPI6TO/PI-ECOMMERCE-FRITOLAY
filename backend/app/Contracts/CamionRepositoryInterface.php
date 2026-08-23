<?php declare(strict_types=1);

namespace App\Contracts;
use App\Models\Camion;
use Illuminate\Support\Collection;

interface CamionRepositoryInterface
{
    public function getActivos(): Collection;
    public function findById(int $id): ?Camion;
    public function create(array $data): Camion;
    public function update(int $id, array $data): Camion;
    public function updateEstado(int $id, string $estado): bool;
    public function asignarChofer(int $camionId, int $choferId): bool;
    public function getAll(): Collection;
}
