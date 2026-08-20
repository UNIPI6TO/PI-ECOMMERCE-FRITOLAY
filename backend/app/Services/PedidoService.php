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

    public function crearPedido(array $data, int $usuarioId): array
    {
        return DB::transaction(function () use ($data, $usuarioId) {
            $subtotal = 0;
            
            foreach ($data['items'] as $item) {
                $producto = $this->productoRepository->findById($item['producto_id']);
                $disponible = $producto->cantidad_fisica - $producto->en_pedidos;
                
                if ($disponible < $item['cantidad']) {
                    throw new Exception("Stock insuficiente para el producto ID: {$item['producto_id']}");
                }
                $subtotal += $producto->precio * $item['cantidad'];
            }

            $descuento = $this->descuentoService->calcularDescuento($usuarioId, $data['metodo_pago'], $subtotal);
            $ivaPorcentaje = config('fritolay.iva_porcentaje', 15);
            $iva = ($subtotal - $descuento) * ($ivaPorcentaje / 100);
            $total = $subtotal - $descuento + $iva;

            $estado = in_array($data['metodo_pago'], ['deposito', 'de_una']) ? 'en_espera_aprobacion' : 'en_espera_asignacion';

            $pedidoData = [
                'cliente_id' => $usuarioId,
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
                $producto = $this->productoRepository->findById($item['producto_id']);
                $itemData = [
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $producto->precio * $item['cantidad'],
                ];
                
                $items[] = $this->pedidoRepository->createItem($itemData);
                $this->inventarioService->incrementarEnPedidos($item['producto_id'], (float)$item['cantidad']);
            }

            $this->auditoriaService->log('pedido_creado', $usuarioId, "Pedido {$pedido->id} creado exitosamente.");

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
        return $this->pedidoRepository->getByCliente($clienteId);
    }
}
