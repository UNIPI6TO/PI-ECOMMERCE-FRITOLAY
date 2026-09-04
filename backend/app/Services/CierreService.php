<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GuiaRepositoryInterface;
use App\Contracts\BodegaRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CierreService
{
    public function __construct(
        private readonly GuiaRepositoryInterface $guiaRepository,
        private readonly BodegaRepositoryInterface $bodegaRepository,
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

            $res = $this->guiaRepository->aprobarRevisionGuia($guiaId, $operadorId);

            if ($res) {
                // Iterar sobre todos los pedidos asociados a la guía para reducir en_pedidos y crear transacción
                foreach ($guia->guiasRuta as $ruta) {
                    foreach ($ruta->asignaciones as $asig) {
                        $pedido = $asig->pedido;
                        if (!$pedido || !$pedido->items) continue;

                        foreach ($pedido->items as $item) {
                            $productoId = (int) $item->producto_id;
                            $cantEntregada = (float) ($item->cantidad_entregada ?? 0);
                            $cantSolicitada = (float) ($item->cantidad_solicitada ?? 0);

                            // Reducir la cantidad reservada en_pedidos por la cantidad entregada
                            if ($cantEntregada > 0) {
                                $this->inventarioService->decrementarEnPedidos($productoId, $cantEntregada);

                                // Registrar la transacción de inventario por la entrega/devolución final
                                DB::table('transacciones_inventario')->insert([
                                    'producto_id' => $productoId,
                                    'camion_id' => $guia->camion_id,
                                    'tipo' => 'EGRESO',
                                    'cantidad' => $cantEntregada,
                                    'motivo' => 'Aprobación de Cierre de Guía #' . $guiaId . ' (Entrega Pedido #' . $pedido->id . ')',
                                    'fecha_transaccion' => now(),
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }
                        }
                    }
                }

                $this->auditoriaService->logSimple('revision_guia_aprobada', 'Se aprobó la revisión de la guía ' . $guiaId, $operadorId);
            }

            return $res;
        });
    }
}
