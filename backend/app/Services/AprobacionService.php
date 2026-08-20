<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PedidoRepositoryInterface;
use App\Contracts\ProductoRepositoryInterface;
use Exception;

class AprobacionService
{
    public function __construct(
        private readonly PedidoRepositoryInterface $pedidoRepository,
        private readonly ProductoRepositoryInterface $productoRepository,
        private readonly AuditoriaService $auditoriaService
    ) {}

    public function aprobar(int $pedidoId, int $operadorId): array
    {
        $pedido = $this->pedidoRepository->findById($pedidoId);

        if (!$pedido || $pedido->estado !== 'en_espera_aprobacion') {
            throw new Exception('El pedido no está en estado válido para aprobación.');
        }

        $pedido = $this->pedidoRepository->update($pedidoId, [
            'estado' => 'en_espera_asignacion'
        ]);

        $this->auditoriaService->log('pedido_aprobado', 'Se aprobó el pedido ' . $pedidoId, $operadorId);

        return $pedido->toArray();
    }

    public function rechazar(int $pedidoId, int $operadorId, string $motivo): array
    {
        $pedido = $this->pedidoRepository->findById($pedidoId);

        if (!$pedido || $pedido->estado !== 'en_espera_aprobacion') {
            throw new Exception('El pedido no está en estado válido para rechazo.');
        }

        $pedido = $this->pedidoRepository->update($pedidoId, [
            'estado' => 'cancelado',
            'motivo_cancelacion' => $motivo
        ]);

        // Libera en_pedidos
        foreach ($pedido->items as $item) {
            $this->productoRepository->liberarEnPedidos($item->producto_id, $item->cantidad);
        }

        $this->auditoriaService->log('pedido_rechazado', 'Se rechazó el pedido ' . $pedidoId . '. Motivo: ' . $motivo, $operadorId);

        return $pedido->toArray();
    }
}
