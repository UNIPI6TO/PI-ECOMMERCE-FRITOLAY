<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PedidoRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\Factura;

class EntregaService
{
    public function __construct(
        private readonly PedidoRepositoryInterface $pedidoRepository,
        private readonly InventarioService $inventarioService,
        private readonly AuditoriaService $auditoriaService
    ) {}

    public function seleccionarPedido(int $pedidoId, int $choferId): object
    {
        $pedido = $this->pedidoRepository->update($pedidoId, ['estado' => 'en_ruta']);
        return $pedido;
    }

    public function registrarEntrega(array $data, int $choferId): array
    {
        $pedido = $this->pedidoRepository->findById((int)$data['pedido_id']);
        
        $tieneDevoluciones = false;
        $totalDevuelto = 0;

        foreach ($data['items'] as $itemData) {
            if (isset($itemData['cantidad_devuelta']) && $itemData['cantidad_devuelta'] > 0) {
                $tieneDevoluciones = true;
                $totalDevuelto += $itemData['cantidad_devuelta'];
            }
        }

        if ($pedido->metodo_pago !== 'efectivo' && $tieneDevoluciones) {
            throw new Exception('No se permiten devoluciones en pedidos pagados con tarjeta.');
        }

        $camionId = DB::table('asignacion_pedido_camion')
            ->where('pedido_id', $pedido->id)
            ->value('camion_id');

        $todosEntregados = true;
        foreach ($data['items'] as $itemData) {
            DB::table('items_pedido')
                ->where('id', $itemData['item_pedido_id'])
                ->update(['cantidad_entregada' => $itemData['cantidad_entregada']]);

            $item = DB::table('items_pedido')->where('id', $itemData['item_pedido_id'])->first();
            
            if ($itemData['cantidad_entregada'] < $item->cantidad) {
                $todosEntregados = false;
            }

            if ($itemData['cantidad_entregada'] > 0) {
                $this->inventarioService->egresoFisicoCamion($camionId, $item->producto_id, $itemData['cantidad_entregada']);
            }
        }

        $nuevoEstado = $todosEntregados ? 'entregado' : 'entregado_parcialmente';
        $pedido = $this->pedidoRepository->update($pedido->id, ['estado' => $nuevoEstado]);

        $facturaId = DB::table('facturas')->insertGetId([
            'pedido_id' => $pedido->id,
            'numero' => 'FAC-' . time(), // Simplified
            'total' => $pedido->total,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->auditoriaService->logSimple('entrega_registrada', 'Entrega de pedido ' . $pedido->id, $choferId);

        return [
            'pedido' => $pedido,
            'factura_data' => [
                'numero' => 'FAC-' . time(),
                'total' => $pedido->total,
                'items' => []
            ]
        ];
    }

    public function getGuiasChofer(int $choferId): \Illuminate\Support\Collection
    {
        $camion = \App\Models\Camion::where('chofer_id', $choferId)->first();
        if (!$camion) return collect([]);
        
        $guiasRuta = \App\Models\GuiaRuta::whereHas('guiaRemision', function ($query) use ($camion) {
            $query->where('camion_id', $camion->id);
            // Mostrar si la remisión está abierta o cerrada (despachada)
        })
        ->where('estado', 'activa') // Solo guías de ruta que aún no se han terminado de entregar
        ->withCount('asignaciones as pedidos_count')
        ->get();
        
        return $guiasRuta->map(function ($guia) {
            return [
                'id' => $guia->id,
                'pedidos_count' => $guia->pedidos_count,
                'fecha' => $guia->fecha_creacion->format('Y-m-d H:i')
            ];
        });
    }

    public function getInventarioCamion(int $camionId): Collection
    {
        return collect([]); // TODO: implement
    }
}
