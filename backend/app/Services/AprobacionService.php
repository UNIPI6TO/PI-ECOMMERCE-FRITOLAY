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

        $this->auditoriaService->log($operadorId, 'pedido_aprobado', 'pedidos', $pedidoId, null, ['estado' => 'en_espera_asignacion']);

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

        // Generar Nota de Crédito (Formato SRI)
        $factura = \App\Models\Factura::where('pedido_id', $pedidoId)->first();
        if ($factura) {
            \App\Models\NotaCredito::create([
                'factura_id' => $factura->id,
                'numero_nota' => \App\Models\NotaCredito::generarNumero($factura->id),
                'fecha_emision' => now(),
                'valor_total' => $factura->total,
                'motivo' => 'Devolución/Cancelación: ' . $motivo
            ]);
        }

        // Libera en_pedidos
        foreach ($pedido->items as $item) {
            $this->productoRepository->decrementarEnPedidos($item->producto_id, (float) $item->cantidad_solicitada);
        }

        $this->auditoriaService->log($operadorId, 'pedido_rechazado', 'pedidos', $pedidoId, null, ['estado' => 'cancelado', 'motivo' => $motivo]);

        return $pedido->toArray();
    }
}
