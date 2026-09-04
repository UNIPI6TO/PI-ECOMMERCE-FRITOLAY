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
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data, $choferId) {
            $pedido = $this->pedidoRepository->findById((int)$data['pedido_id']);
            
            if (in_array($pedido->estado, ['entregado', 'entregado_parcialmente'])) {
                throw new Exception('Este pedido ya ha sido entregado previamente.');
            }
            
            $tieneDevoluciones = false;
            $todosDevueltos = true;
            $montoDevuelto = 0;

            foreach ($data['items'] as $itemData) {
                if (($itemData['cantidad_entregada'] ?? 0) > 0) {
                    $todosDevueltos = false;
                }
                if (isset($itemData['cantidad_devuelta']) && $itemData['cantidad_devuelta'] > 0) {
                    $tieneDevoluciones = true;
                    $item = DB::table('items_pedido')->where('id', $itemData['item_pedido_id'])->first();
                    if ($item) {
                        $montoDevuelto += ($itemData['cantidad_devuelta'] * (float)$item->precio_unitario);
                    }
                }
            }

            if (!$todosDevueltos && strtolower((string)$pedido->metodo_pago) !== 'efectivo' && $tieneDevoluciones) {
                throw new Exception('No se permiten devoluciones parciales en pedidos pagados con tarjeta o transferencia.');
            }

            $asignacion = \App\Models\AsignacionPedidoCamion::with('guiaRuta.guiaRemision')
                ->where('pedido_id', $pedido->id)
                ->first();
                
            if (!$asignacion || !$asignacion->guiaRuta || !$asignacion->guiaRuta->guiaRemision) {
                throw new Exception("No se encontró la ruta asignada a este pedido.");
            }
            
            $camionId = $asignacion->guiaRuta->guiaRemision->camion_id;
            $todosEntregados = true;
            $motivoPrincipal = $data['motivo_no_entrega'] ?? ($data['items'][0]['motivo_devolucion'] ?? 'Devolución de pedido');

            foreach ($data['items'] as $itemData) {
                DB::table('items_pedido')
                    ->where('id', $itemData['item_pedido_id'])
                    ->update(['cantidad_entregada' => $itemData['cantidad_entregada']]);

                $item = DB::table('items_pedido')->where('id', $itemData['item_pedido_id'])->first();
                if (!$item) continue;
                
                if ($itemData['cantidad_entregada'] < $item->cantidad_solicitada) {
                    $todosEntregados = false;
                    
                    // Si se devuelve, registrar la mercaderia en mal estado si aplica
                    if (!empty($itemData['estado_mercaderia']) && $itemData['estado_mercaderia'] === 'mal_estado') {
                        DB::table('mercaderia_mal_estado')->insert([
                            'guia_ruta_id' => $asignacion->guia_ruta_id,
                            'producto_id' => $item->producto_id,
                            'cantidad' => $item->cantidad_solicitada - $itemData['cantidad_entregada'],
                            'motivo' => $itemData['motivo_devolucion'] ?? $motivoPrincipal,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }

                if ($itemData['cantidad_entregada'] > 0) {
                    $this->inventarioService->egresoFisicoCamion($camionId, $item->producto_id, (float)$itemData['cantidad_entregada']);
                }
            }

            if ($todosDevueltos) {
                $nuevoEstado = 'no_entregado';
                // Restaurar el inventario reservado en pedidos
                foreach ($data['items'] as $itemData) {
                    $item = DB::table('items_pedido')->where('id', $itemData['item_pedido_id'])->first();
                    if ($item) {
                        $this->inventarioService->decrementarEnPedidos((int)$item->producto_id, (float)$item->cantidad_solicitada);
                    }
                }
            } else {
                $nuevoEstado = $todosEntregados ? 'entregado' : 'entregado_parcialmente';
            }

            $pedido = $this->pedidoRepository->update($pedido->id, [
                'estado' => $nuevoEstado,
                'fecha_entrega' => now(),
                'motivo_cancelacion' => $motivoPrincipal
            ]);
            
            $estadoAsig = $todosDevueltos ? \App\Models\AsignacionPedidoCamion::ESTADO_NO_ENTREGADO : \App\Models\AsignacionPedidoCamion::ESTADO_ENTREGADO;
            \Illuminate\Support\Facades\DB::table('asignacion_pedido_camion')
                ->where('pedido_id', $pedido->id)
                ->update([
                    'estado' => $estadoAsig,
                    'fecha_entrega' => now(),
                    'updated_at' => now()
                ]);

            $fechaPedido = $pedido->creado_en ?? $pedido->created_at ?? now();

            $factura = DB::table('facturas')->where('pedido_id', $pedido->id)->first();
            if (!$factura) {
                $numeroFactura = 'FAC-' . date('Y') . '-' . str_pad((string)$pedido->id, 6, '0', STR_PAD_LEFT);
                $facturaId = DB::table('facturas')->insertGetId([
                    'pedido_id' => $pedido->id,
                    'numero_factura' => $numeroFactura,
                    'subtotal' => $pedido->subtotal,
                    'iva' => $pedido->iva,
                    'total' => $pedido->total,
                    'fecha_pedido' => $fechaPedido,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $facturaId = $factura->id;
                $numeroFactura = $factura->numero_factura;
            }
            
            if ($tieneDevoluciones || $todosDevueltos) {
                $valorNota = $todosDevueltos ? $pedido->total : ($montoDevuelto * 1.15);
                $existente = DB::table('notas_credito')->where('factura_id', $facturaId)->first();
                if (!$existente) {
                    DB::table('notas_credito')->insert([
                        'factura_id' => $facturaId,
                        'numero_nota' => 'NC-' . date('Y') . '-' . str_pad((string)$facturaId, 6, '0', STR_PAD_LEFT),
                        'fecha_emision' => now()->toDateString(),
                        'valor_total' => $valorNota,
                        'motivo' => 'Devolución en entrega - ' . $motivoPrincipal,
                        'fecha_pedido' => $fechaPedido,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            $this->auditoriaService->logSimple('entrega_registrada', 'Entrega/Devolución de pedido ' . $pedido->id, $choferId);

            return [
                'pedido' => $pedido,
                'factura_data' => [
                    'numero' => $numeroFactura,
                    'total' => $pedido->total,
                    'items' => []
                ]
            ];
        });
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
        
        $guiasRuta->load('asignaciones.pedido');
        return $guiasRuta->map(function ($guia) {
            $efectivo = 0;
            $tarjeta = 0;
            $transferencia = 0;
            $total = 0;
            
            foreach ($guia->asignaciones as $asignacion) {
                if ($asignacion->pedido) {
                    $monto = (float) $asignacion->pedido->total;
                    $metodo = strtoupper($asignacion->pedido->metodo_pago);
                    
                    if ($metodo === 'EFECTIVO') $efectivo += $monto;
                    elseif (in_array($metodo, ['TC', 'TD'])) $tarjeta += $monto;
                    elseif (in_array($metodo, ['DEPOSITO', 'DE_UNA'])) $transferencia += $monto;
                    
                    $total += $monto;
                }
            }
            
            return [
                'id' => $guia->id,
                'pedidos_count' => $guia->pedidos_count,
                'fecha' => $guia->fecha_creacion->format('Y-m-d H:i'),
                'recaudacion_esperada' => [
                    'efectivo' => $efectivo,
                    'tarjeta' => $tarjeta,
                    'transferencia' => $transferencia,
                    'total' => $total
                ]
            ];
        });
    }

        public function getPedidosGuiaChofer(int $guiaId): \Illuminate\Support\Collection
    {
        $guiaRuta = \App\Models\GuiaRuta::with(['asignaciones' => function($q) {
            $q->orderBy('orden', 'asc');
        }, 'asignaciones.pedido.cliente', 'asignaciones.pedido.direccion', 'asignaciones.pedido.items.producto', 'asignaciones.pedido.cliente.usuario'])->find($guiaId);
        
        if (!$guiaRuta) return collect([]);
        
        return $guiaRuta->asignaciones->map(function ($asig) {
            $p = $asig->pedido;
            $nombre = $p->cliente->razon_social ?: $p->cliente->nombre_cliente;
            if (!$nombre && $p->cliente->usuario) {
                $nombre = $p->cliente->usuario->nombre;
            }
            
                        return [
                'id' => $p->id,
                'numero_pedido' => $p->numero_pedido,
                'fecha_emision' => $p->created_at ? $p->created_at->format('Y-m-d') : date('Y-m-d'),
                'cliente' => $nombre ?? 'Sin Cliente',
                'identificacion' => $p->cliente->ruc ?? $p->cliente->cedula ?? '9999999999',
                'telefono' => $p->cliente->telefono ?? ($p->cliente->usuario->telefono ?? ''),
                'direccion' => $p->direccion->descripcion ?? 'Ubicación Desconocida',
                'lat' => $p->direccion->latitud,
                'lng' => $p->direccion->longitud,
                'estado' => $asig->estado,
                'orden' => $asig->orden,
                'subtotal' => $p->subtotal,
                'iva' => $p->iva,
                'total' => $p->total,
                'metodo_pago' => $p->metodo_pago,
                'items' => $p->items->map(function($item) {
                    $cant = (int) ($item->cantidad_solicitada ?? $item->cantidad ?? 0);
                    $precio = (float) ($item->precio_unitario ?? 0);
                    $subtotal = $cant * $precio;
                    return [
                        'id' => $item->id,
                        'item_pedido_id' => $item->id,
                        'producto_id' => $item->producto_id,
                        'producto' => $item->producto->nombre ?? 'Producto',
                        'cantidad' => $cant,
                        'cantidad_solicitada' => $cant,
                        'precio_unitario' => $precio,
                        'subtotal' => $subtotal
                    ];
                })->toArray()
            ];
        });
    }

    public function getInventarioCamion(int $camionId): Collection
    {
        return collect([]); // TODO: implement
    }
}
