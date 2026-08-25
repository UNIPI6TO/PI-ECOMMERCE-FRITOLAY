<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PedidoRepositoryInterface;
use App\Contracts\CamionRepositoryInterface;
use App\Contracts\GuiaRepositoryInterface;
use App\Contracts\BodegaRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;

class RutaService
{
    public function __construct(
        private readonly PedidoRepositoryInterface $pedidoRepository,
        private readonly CamionRepositoryInterface $camionRepository,
        private readonly GuiaRepositoryInterface $guiaRepository,
        private readonly BodegaRepositoryInterface $bodegaRepository,
        private readonly InventarioService $inventarioService,
        private readonly AuditoriaService $auditoriaService
    ) {}

    public function crearAsignacion(array $pedidoIds, int $camionId, int $operadorId): array
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($camionId, $pedidoIds, $operadorId) {
            $camion = $this->camionRepository->findById($camionId);
            if (!$camion || $camion->estado !== 'activo') {
                throw new Exception('El camión no está activo.', 422);
            }
    
            foreach ($pedidoIds as $pedidoId) {
                $pedido = $this->pedidoRepository->findById((int)$pedidoId);
                if ($this->pedidoRepository->isAsignado((int)$pedidoId)) {
                    throw new Exception('El pedido ' . $pedidoId . ' ya está asignado.', 409);
                }
            }
    
            $guiaRemision = $this->guiaRepository->createRemision(['camion_id' => $camionId, 'estado' => 'abierta', 'operador_id' => $operadorId]);
            $guiaRuta = $this->guiaRepository->createRuta(['guia_remision_id' => $guiaRemision->id]);
    
            $asignaciones = [];
            $orden = 1;
            foreach ($pedidoIds as $pedidoId) {
                $asignaciones[] = \App\Models\AsignacionPedidoCamion::create([
                    'pedido_id' => (int)$pedidoId,
                    'guia_ruta_id' => $guiaRuta->id,
                    'orden' => $orden++,
                    'estado' => \App\Models\AsignacionPedidoCamion::ESTADO_ASIGNADO
                ]);
                $pedido = $this->pedidoRepository->update((int)$pedidoId, ['estado' => 'listo_para_entregar']);
                
                foreach ($pedido->items as $item) {
                    $this->inventarioService->ingresoFisicoCamion($camionId, (int)$item->producto_id, (float)$item->cantidad_solicitada);
                }
            }
    
            $this->auditoriaService->logSimple('asignacion_ruta', 'Se asignaron pedidos al camión ' . $camionId, $operadorId);
    
            return [
                'guia_remision' => $guiaRemision,
                'guia_ruta' => $guiaRuta,
                'asignaciones' => $asignaciones
            ];
        });
    }

    public function getAsignacion(int $guiaRemisionId): array
    {
        return $this->guiaRepository->getDetalleRemision($guiaRemisionId);
    }

    public function getCamionesActivos(): Collection
    {
        return $this->camionRepository->findActivos();
    }

    public function getPedidosEnEspera(array $filtros): Collection
    {
        return $this->pedidoRepository->findEnEspera($filtros);
    }

    public function cancelarAsignacion(array $pedidoIds): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($pedidoIds) {
            foreach ($pedidoIds as $pedidoId) {
                $pedidoId = (int)$pedidoId;
                
                // Find active assignment for this order
                $asignacion = \App\Models\AsignacionPedidoCamion::where('pedido_id', $pedidoId)
                    ->whereIn('estado', [\App\Models\AsignacionPedidoCamion::ESTADO_ASIGNADO, \App\Models\AsignacionPedidoCamion::ESTADO_EN_RUTA])
                    ->first();
                    
                if ($asignacion) {
                    $camionId = $asignacion->guiaRuta->guiaRemision->camion_id;
                    
                    // Revert inventory
                    $pedido = $this->pedidoRepository->findById($pedidoId);
                    foreach ($pedido->items as $item) {
                        $this->inventarioService->revertirIngresoFisicoCamion($camionId, (int)$item->producto_id, (float)$item->cantidad_solicitada);
                    }
                    
                    // Delete assignment and update order
                    $asignacion->delete();
                    $this->pedidoRepository->update($pedidoId, ['estado' => 'en_espera_asignacion']);
                }
            }
        });
    }

    public function cerrarRuta(int $camionId): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($camionId) {
            // Find active guia remision for truck
            $guiaRemision = \App\Models\GuiaRemision::where('camion_id', $camionId)
                ->where('estado', 'abierta')
                ->first();
                
            if (!$guiaRemision) {
                throw new Exception('No hay ruta abierta para este camión.');
            }
            
            $guiaRemision->update(['estado' => 'cerrada']);
            
            foreach ($guiaRemision->guiasRuta as $guiaRuta) {
                foreach ($guiaRuta->asignaciones as $asignacion) {
                    if ($asignacion->estado !== \App\Models\AsignacionPedidoCamion::ESTADO_ENTREGADO) {
                        $asignacion->update(['estado' => \App\Models\AsignacionPedidoCamion::ESTADO_ENTREGADO]);
                        $this->pedidoRepository->update($asignacion->pedido_id, ['estado' => 'entregado']);
                        
                        // Decrement 'en_pedidos' and physical stock via egresoFisicoCamion
                        $pedido = $this->pedidoRepository->findById($asignacion->pedido_id);
                        foreach ($pedido->items as $item) {
                            $this->inventarioService->egresoFisicoCamion($camionId, (int)$item->producto_id, (float)$item->cantidad_solicitada);
                        }
                    }
                }
            }
        });
    }

}
