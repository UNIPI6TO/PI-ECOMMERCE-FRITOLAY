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
        $this->productoRepository->update($productoId, ['en_pedidos' => $producto->en_pedidos]);
    }

    public function decrementarEnPedidos(int $productoId, float $cantidad): void
    {
        $producto = $this->productoRepository->findById($productoId);
        $producto->en_pedidos -= $cantidad;
        $this->productoRepository->update($productoId, ['en_pedidos' => $producto->en_pedidos]);
    }

    public function egresoFisicoCamion(int $camionId, int $productoId, float $cantidad): void
    {
        DB::transaction(function () use ($camionId, $productoId, $cantidad) {
            $this->bodegaRepository->decrement($camionId, $productoId, $cantidad);
            $this->decrementarEnPedidos($productoId, $cantidad);
            
            DB::table('transacciones')->insert([
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
            $this->bodegaRepository->increment($camionId, $productoId, $cantidad);
            
            DB::table('transacciones')->insert([
                'tipo' => 'INGRESO',
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
            $this->productoRepository->update($productoId, ['cantidad_fisica' => $producto->cantidad_fisica]);
            
            DB::table('transacciones')->insert([
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
