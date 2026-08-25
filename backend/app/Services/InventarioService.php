<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ProductoRepositoryInterface;
use App\Contracts\BodegaRepositoryInterface;
use Illuminate\Support\Facades\DB;

class InventarioService
{
    public function __construct(
        private readonly ProductoRepositoryInterface $productoRepository,
        private readonly BodegaRepositoryInterface $bodegaRepository
    ) {
    }

    public function incrementarEnPedidos(int $productoId, float $cantidad): void
    {
        $producto = $this->productoRepository->findById($productoId);
        $producto->en_pedidos += $cantidad;
        $producto->save();
    }

    public function decrementarEnPedidos(int $productoId, float $cantidad): void
    {
        $producto = $this->productoRepository->findById($productoId);
        $producto->en_pedidos -= $cantidad;
        $producto->save();
    }

    public function egresoFisicoCamion(int $camionId, int $productoId, float $cantidad): void
    {
        DB::transaction(function () use ($camionId, $productoId, $cantidad) {
            $this->bodegaRepository->decrementar($camionId, $productoId, $cantidad);
            $this->decrementarEnPedidos($productoId, $cantidad);
            
            DB::table('transacciones_inventario')->insert([
                'motivo' => 'Movimiento de ruta',
                'tipo' => 'EGRESO',
                'camion_id' => $camionId,
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function ingresoFisicoCamion(int $camionId, int $productoId, float $cantidad): void
    {
        DB::transaction(function () use ($camionId, $productoId, $cantidad) {
            $this->bodegaRepository->incrementar($camionId, $productoId, $cantidad);
            
            DB::table('transacciones_inventario')->insert([
                'motivo' => 'Movimiento de ruta',
                'tipo' => 'INGRESO',
                'camion_id' => $camionId,
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function revertirIngresoFisicoCamion(int $camionId, int $productoId, float $cantidad): void
    {
        DB::transaction(function () use ($camionId, $productoId, $cantidad) {
            $this->bodegaRepository->decrementar($camionId, $productoId, $cantidad);
            
            DB::table('transacciones_inventario')->insert([
                'motivo' => 'Reversión de asignación de ruta',
                'tipo' => 'EGRESO',
                'camion_id' => $camionId,
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function ingresoMaestro(int $productoId, float $cantidad, string $motivo): void
    {
        DB::transaction(function () use ($productoId, $cantidad, $motivo) {
            $producto = $this->productoRepository->findById($productoId);
            $producto->cantidad_fisica += $cantidad;
            $producto->save();
            
            DB::table('transacciones_inventario')->insert([
                'motivo' => 'Movimiento de ruta',
                'tipo' => 'INGRESO',
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
                'motivo' => $motivo,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function enceraBodegaCamion(int $camionId): void
    {
        $this->bodegaRepository->encerar($camionId);
    }
}
