<?php declare(strict_types=1);

namespace App\Contracts;
use App\Models\Producto;
use Illuminate\Support\Collection;

interface ProductoRepositoryInterface
{
    public function getAllWithStock(array $filters = [], string $orderBy = 'nombre'): Collection;
    public function findById(int $id): ?Producto;
    public function getStockDisponible(int $productoId): float;
    public function decrementarEnPedidos(int $productoId, float $cantidad): void;
    public function incrementarEnPedidos(int $productoId, float $cantidad): void;
    public function decrementarCantidadFisica(int $productoId, float $cantidad): void;
    public function incrementarCantidadFisica(int $productoId, float $cantidad): void;
}
