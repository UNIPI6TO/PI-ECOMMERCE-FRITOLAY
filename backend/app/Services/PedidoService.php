<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PedidoRepositoryInterface;
use App\Contracts\ProductoRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class PedidoService
{
    public function __construct(
        private readonly PedidoRepositoryInterface $pedidoRepository,
        private readonly ProductoRepositoryInterface $productoRepository,
        private readonly InventarioService $inventarioService,
        private readonly DescuentoService $descuentoService,
        private readonly AuditoriaService $auditoriaService
    ) {
    }

    public function crearPedido(array $data, int $clienteId, int $usuarioId): array
    {
        return DB::transaction(function () use ($data, $clienteId, $usuarioId) {
            $subtotal = 0;
            
            foreach ($data['items'] as $item) {
                $productoId = (int) $item['producto_id'];
                $cantidad = (float) $item['cantidad'];

                $producto = $this->productoRepository->findById($productoId);
                $disponible = $producto->cantidad_fisica - $producto->en_pedidos;
                
                if ($disponible < $cantidad) {
                    throw new Exception("Stock insuficiente para el producto ID: {$productoId}");
                }
                $subtotal += $producto->precio * $cantidad;
            }

            $descuento = $this->descuentoService->calcularDescuento($usuarioId, $data['metodo_pago'], $subtotal);
            $ivaPorcentaje = config('fritolay.iva_porcentaje', 15);
            $iva = ($subtotal - $descuento) * ($ivaPorcentaje / 100);
            $total = $subtotal - $descuento + $iva;

            $estado = in_array($data['metodo_pago'], ['deposito', 'de_una']) ? 'en_espera_aprobacion' : 'en_espera_asignacion';

            $pedidoData = [
                'cliente_id' => $clienteId,
                'direccion_id' => $data['direccion_id'],
                'metodo_pago' => $data['metodo_pago'],
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'iva' => $iva,
                'total' => $total,
                'estado' => $estado,
            ];

            $pedido = $this->pedidoRepository->create($pedidoData);
            $items = [];

            foreach ($data['items'] as $item) {
                $productoId = (int) $item['producto_id'];
                $cantidad = (float) $item['cantidad'];

                $producto = $this->productoRepository->findById($productoId);
                $itemData = [
                    'pedido_id' => $pedido->id,
                    'producto_id' => $productoId,
                    'cantidad_solicitada' => $cantidad,
                    'cantidad_entregada' => 0,
                    'precio_unitario' => $producto->precio,
                    'descuento_aplicado' => 0,
                    'fecha_pedido' => $pedido->creado_en ?? $pedido->created_at ?? now(),
                ];
                
                $items[] = $pedido->items()->create($itemData);
                $this->inventarioService->incrementarEnPedidos($productoId, $cantidad);
            }

            $this->auditoriaService->log($usuarioId, 'pedido_creado', 'pedidos', $pedido->id);

            // Crear Factura
            $factura = \App\Models\Factura::create([
                'pedido_id' => $pedido->id,
                'numero_factura' => \App\Models\Factura::generarNumero($pedido->id),
                'fecha_emision' => now(),
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $total,
                'fecha_pedido' => $pedido->creado_en ?? $pedido->created_at ?? now(),
            ]);
            $pedido->setRelation('factura', $factura);

            return ['pedido' => $pedido, 'items' => $items];
        });
    }

    public function getPedido(int $pedidoId, int $clienteId)
    {
        $pedido = $this->pedidoRepository->findById($pedidoId);
        if (!$pedido || $pedido->cliente_id !== $clienteId) {
            throw new Exception("Pedido no encontrado o no pertenece al cliente.");
        }
        return $pedido;
    }

    public function getHistorial(int $clienteId): Collection
    {
        return \App\Models\Pedido::where('cliente_id', $clienteId)
            ->with(['items.producto', 'cliente', 'factura.notaCredito', 'direccion'])
            ->orderBy('id', 'desc')
            ->get();
    }

    public function cancelarPedido(int $pedidoId, int $usuarioId, ?string $motivo = null): void
    {
        DB::transaction(function () use ($pedidoId, $usuarioId, $motivo) {
            $cliente = \App\Models\Cliente::where('usuario_id', $usuarioId)->first();
            if (!$cliente) throw new Exception("Cliente no encontrado.");

            $pedido = $this->getPedido($pedidoId, $cliente->id);

            if (in_array($pedido->estado, ['en_ruta', 'listo_para_entregar', 'entregado', 'entregado_parcialmente', 'cancelado'])) {
                throw new Exception("El pedido no puede ser cancelado en su estado actual.");
            }

            $motivoTexto = $motivo ?: 'Cancelado por el cliente';

            $pedido->estado = 'cancelado';
            $pedido->motivo_cancelacion = $motivoTexto;
            $pedido->save();

            // Generar Nota de Crédito (Formato SRI)
            $factura = \App\Models\Factura::where('pedido_id', $pedidoId)->first();
            if ($factura) {
                \App\Models\NotaCredito::create([
                    'factura_id' => $factura->id,
                    'numero_nota' => \App\Models\NotaCredito::generarNumero($factura->id),
                    'fecha_emision' => now(),
                    'valor_total' => $factura->total,
                    'motivo' => 'Cancelación: ' . $motivoTexto,
                    'fecha_pedido' => $pedido->creado_en ?? $pedido->created_at ?? now()
                ]);
            }

            // Liberar inventario
            foreach ($pedido->items as $item) {
                $this->inventarioService->decrementarEnPedidos($item->producto_id, $item->cantidad_solicitada);
            }

            $this->auditoriaService->log($usuarioId, 'pedido_cancelado', 'pedidos', $pedido->id);
        });
    }
}
