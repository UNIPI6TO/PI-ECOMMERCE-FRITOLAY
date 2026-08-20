<?php declare(strict_types=1);

namespace App\Contracts;
use App\Models\TransaccionInventario;
use Illuminate\Support\Collection;

interface BodegaRepositoryInterface
{
    public function upsert(int $camionId, int $productoId, float $cantidad): void;
    public function decrementar(int $camionId, int $productoId, float $cantidad): void;
    public function incrementar(int $camionId, int $productoId, float $cantidad): void;
    public function encerar(int $camionId): void;
    public function getByCamion(int $camionId): Collection;
    public function createTransaccion(array $data): TransaccionInventario;
}
