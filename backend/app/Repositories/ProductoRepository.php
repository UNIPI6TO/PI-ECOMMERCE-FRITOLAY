<?php declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\ProductoRepositoryInterface;
use App\Models\Producto;
use Illuminate\Support\Collection;

class ProductoRepository implements ProductoRepositoryInterface
{
    public function getAllWithStock(array $filters = [], string $orderBy = 'nombre'): Collection
    {
        $query = Producto::query();
        
        if (isset($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }
        
        return $query->orderBy($orderBy)->get();
    }

    public function findById(int $id): ?Producto
    {
        return Producto::find($id);
    }

    public function getStockDisponible(int $productoId): float
    {
        $producto = $this->findById($productoId);
        return $producto ? $producto->getDisponibleAttribute() : 0.0;
    }

    public function decrementarEnPedidos(int $productoId, float $cantidad): void
    {
        Producto::where('id', $productoId)->decrement('en_pedidos', $cantidad);
    }

    public function incrementarEnPedidos(int $productoId, float $cantidad): void
    {
        Producto::where('id', $productoId)->increment('en_pedidos', $cantidad);
    }

    public function decrementarCantidadFisica(int $productoId, float $cantidad): void
    {
        Producto::where('id', $productoId)->decrement('cantidad_fisica', $cantidad);
    }

    public function incrementarCantidadFisica(int $productoId, float $cantidad): void
    {
        Producto::where('id', $productoId)->increment('cantidad_fisica', $cantidad);
    }
}
