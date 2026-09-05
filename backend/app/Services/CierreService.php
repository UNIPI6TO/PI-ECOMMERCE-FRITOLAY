<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GuiaRepositoryInterface;
use App\Contracts\BodegaRepositoryInterface;
use App\Contracts\ProductoRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CierreService
{
    public function __construct(
        private readonly GuiaRepositoryInterface $guiaRepository,
        private readonly BodegaRepositoryInterface $bodegaRepository,
        private readonly ProductoRepositoryInterface $productoRepository,
        private readonly InventarioService $inventarioService,
        private readonly AuditoriaService $auditoriaService
    ) {}

    public function getResumenCaja(int $guiaRutaId): array
    {
        return $this->guiaRepository->getResumenCaja($guiaRutaId);
    }

            public function declararArqueo(int $guiaRutaId, float $efectivoDeclarado, int $choferId): object
    {
        return DB::transaction(function () use ($guiaRutaId, $efectivoDeclarado, $choferId) {
            $ruta = \App\Models\GuiaRuta::find($guiaRutaId);
            if (!$ruta) throw new \Exception('Guía de ruta no encontrada');
            
            $guiaRemisionId = $ruta->guia_remision_id;

            // El requerimiento dice: "Únicamente el chofer podrá marcarla como 'Cerrada' desde su propia sesión/dispositivo."
            $this->guiaRepository->updateRemision($guiaRemisionId, [
                'estado' => 'cerrada',
                'efectivo_declarado' => $efectivoDeclarado
            ]);
            
            // Also close the related Guias Ruta
            $g = \App\Models\GuiaRemision::with('guiasRuta')->find($guiaRemisionId);
            if ($g) {
                foreach ($g->guiasRuta as $r) {
                    $r->update(['estado' => 'cerrada']);
                }
                // Devolver mercaderia no entregada a la bodega principal
                $this->inventarioService->enceraBodegaCamion($g->camion_id);
            }
            
            return $g;
        });
    }

    public function procesarMercaderiaDevuelta(int $guiaRutaId, array $mercaderias, int $operadorId): void
    {
        DB::transaction(function () use ($guiaRutaId, $mercaderias, $operadorId) {
            foreach ($mercaderias as $mercaderia) {
                if ($mercaderia['estado'] === 'buen_estado') {
                    $this->inventarioService->ingresoMaestro($mercaderia['producto_id'], $mercaderia['cantidad'], $mercaderia['motivo'] ?? 'Devolución en buen estado');
                } else {
                    DB::table('mercaderia_mal_estado')->insert([
                        'guia_ruta_id' => $guiaRutaId,
                        'producto_id' => $mercaderia['producto_id'],
                        'cantidad' => $mercaderia['cantidad'],
                        'motivo' => $mercaderia['motivo'],
                        'reportado_por' => $operadorId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        });
    }

    public function cerrarGuia(int $guiaRemisionId, float $efectivoRecibido, int $operadorId): object
    {
        return DB::transaction(function () use ($guiaRemisionId, $efectivoRecibido, $operadorId) {
            $guia = $this->guiaRepository->findByIdRemision($guiaRemisionId);
            if (!$guia || $guia->estado !== 'confirmacion_cierre') {
                throw new Exception('La guía no está lista para cierre.');
            }

            $guia = $this->guiaRepository->updateRemision($guiaRemisionId, [
                'estado' => 'cerrada',
                'efectivo_recibido' => $efectivoRecibido
            ]);

            $this->inventarioService->encerarBodegaCamion($guia->camion_id);

            $this->auditoriaService->logSimple('cierre_guia', 'Se cerró la guía de remisión ' . $guiaRemisionId, $operadorId);

            return $guia;
        });
    }

    public function getPendientesCierre(): Collection
    {
        return $this->guiaRepository->getPendientesCierre();
    }

    public function getGuiasResumen(array $filtros): Collection
    {
        return $this->guiaRepository->getGuiasResumen($filtros);
    }

    public function getDetalleGuiaCierre(int $guiaId): array
    {
        return $this->guiaRepository->getDetalleGuiaCierre($guiaId);
    }

    public function aprobarRevisionGuia(int $guiaId, int $operadorId): bool
    {
        return DB::transaction(function () use ($guiaId, $operadorId) {
            $guia = \App\Models\GuiaRemision::with('guiasRuta.asignaciones.pedido.items')->find($guiaId);
            if (!$guia) {
                throw new Exception('Guía de remisión no encontrada.');
            }

            if ($guia->estado === \App\Models\GuiaRemision::ESTADO_REVISADA) {
                throw new Exception('Esta guía ya fue revisada y aprobada previamente.');
            }

            // Recorrer todos los pedidos asociados a la guía efectivamente entregados
            foreach ($guia->guiasRuta as $guiaRuta) {
                foreach ($guiaRuta->asignaciones as $asignacion) {
                    $pedido = $asignacion->pedido;
                    if (!$pedido) {
                        continue;
                    }

                    // Procesar solo los pedidos entregados (totales o parciales)
                    if (in_array($pedido->estado, ['entregado', 'entregado_parcialmente'])) {
                        foreach ($pedido->items as $item) {
                            $cantidadEntregada = (float) $item->cantidad_entregada;
                            if ($cantidadEntregada > 0) {
                                // Descontar exclusivamente la mercancía real entregada del inventario maestro
                                $producto = $this->productoRepository->findById((int) $item->producto_id);
                                if (!$producto) {
                                    throw new Exception("Producto ID {$item->producto_id} no encontrado durante la aprobación.");
                                }

                                if ($producto->cantidad_fisica < $cantidadEntregada) {
                                    throw new Exception("Stock insuficiente en inventario maestro para el producto '{$producto->nombre}'. Disponible: {$producto->cantidad_fisica}, Requerido: {$cantidadEntregada}");
                                }

                                $this->productoRepository->decrementarCantidadFisica((int) $item->producto_id, $cantidadEntregada);

                                // Registrar la transacción de egreso maestro por venta final entregada
                                DB::table('transacciones_inventario')->insert([
                                    'motivo' => "Venta entregada - Guía #{$guiaId} / Pedido #{$pedido->id}",
                                    'tipo' => 'EGRESO',
                                    'producto_id' => $item->producto_id,
                                    'cantidad' => $cantidadEntregada,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
                        }
                    }
                }
            }

            $res = $this->guiaRepository->aprobarRevisionGuia($guiaId, $operadorId);

            if ($res) {
                $this->auditoriaService->logSimple('revision_guia_aprobada', 'Se aprobó la revisión de la guía ' . $guiaId, $operadorId);
            }

            return $res;
        });
    }
}
