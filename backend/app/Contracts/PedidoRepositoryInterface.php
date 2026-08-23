<?php declare(strict_types=1);

namespace App\Contracts;
use App\Models\Pedido;
use Illuminate\Support\Collection;

interface PedidoRepositoryInterface
{
    public function create(array $data): Pedido;
    public function findById(int $id): ?Pedido;
    public function update(int $id, array $data): Pedido;
    public function findByCliente(int $clienteId): Collection;
    public function updateEstado(int $id, string $estado): bool;
    public function getByEstado(string $estado, array $filtrosFecha = []): Collection;
    public function getByEstados(array $estados, array $filtrosFecha = []): Collection;
    public function isAsignado(int $pedidoId): bool;
    public function countByEstado(): array;
}
