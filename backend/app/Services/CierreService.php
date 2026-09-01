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

    public function declararArqueo(int $guiaRemisionId, float $efectivoDeclarado, int $choferId): object
    {
        // El requerimiento dice: "únicamente el chofer podrá marcarla como 'Cerrada' desde su propia sesión/dispositivo."
        $guia = $this->guiaRepository->updateRemision($guiaRemisionId, [
            'estado' => 'cerrada',
            'efectivo_declarado' => $efectivoDeclarado
        ]);
        
        // Also close the related Guias Ruta
        $g = \App\Models\GuiaRemision::with('guiasRuta')->find($guiaRemisionId);
        if ($g) {
            foreach ($g->guiasRuta as $ruta) {
                $ruta->update(['estado' => 'cerrada']);
            }
        }
        
        return $guia;
    }

    public function procesarMercaderiaDevuelta(int $guiaRutaId, array $mercaderias, int $operadorId): void
    {
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
    }

    public function cerrarGuia(int $guiaRemisionId, float $efectivoRecibido, int $operadorId): object
    {
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
    }

    public function getPendientesCierre(): Collection
    {
        return $this->guiaRepository->getPendientesCierre();
    }
}
