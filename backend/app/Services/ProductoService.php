<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ProductoRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductoService
{
    public function __construct(private readonly ProductoRepositoryInterface $productoRepository)
    {
    }

    public function getCatalogo(array $filters, string $orderBy = 'nombre', string $orden = 'asc'): Collection
    {
        $productos = $this->productoRepository->getAllWithStock($filters, $orderBy);

        return $productos->map(function ($producto) {
            $disponible = $producto->cantidad_fisica - $producto->en_pedidos;
            $producto->disponible = $disponible;
            $umbral = $producto->umbral ?? 0.2; 
            $producto->bajo_stock = $disponible < ($producto->cantidad_fisica * $umbral);
            $producto->sin_stock = $disponible <= 0;
            return $producto;
        });
    }

    public function getProducto(int $id): array
    {
        $producto = $this->productoRepository->findById($id);
        
        if (!$producto) {
            throw new ModelNotFoundException("Producto no encontrado");
        }
        
        $disponible = $producto->cantidad_fisica - $producto->en_pedidos;
        $producto->disponible = $disponible;
        $umbral = $producto->umbral ?? 0.2;
        $producto->bajo_stock = $disponible < ($producto->cantidad_fisica * $umbral);
        $producto->sin_stock = $disponible <= 0;

        return $producto->toArray();
    }

    public function getStock(int $id): float
    {
        $producto = $this->productoRepository->findById($id);
        
        if (!$producto) {
            throw new ModelNotFoundException("Producto no encontrado");
        }

        return (float) ($producto->cantidad_fisica - $producto->en_pedidos);
    }
}
