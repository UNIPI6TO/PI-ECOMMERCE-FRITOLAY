<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ReporteRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        private readonly ReporteRepository $reporteRepository
    ) {}

    public function getKpis(array $filtrosFecha): array
    {
        $inicio = Carbon::parse($filtrosFecha['fecha_inicio'] ?? now())->startOfDay();
        $fin = Carbon::parse($filtrosFecha['fecha_fin'] ?? now())->endOfDay();

        $porEstado = $this->reporteRepository->getPedidosCountPorEstado($inicio, $fin);
        
        $total = array_sum($porEstado);
        $entregados = ($porEstado['entregado'] ?? 0) + ($porEstado['entregado_parcialmente'] ?? 0);
        $efectividad = $total > 0 ? ($entregados / $total) * 100 : 0;

        return [
            'pedidos_por_estado' => $porEstado,
            'efectividad_general' => round($efectividad, 2),
            'efectividad_por_camion' => [] // TODO
        ];
    }

    public function getVentas(array $filtrosFecha): array
    {
        $inicio = Carbon::parse($filtrosFecha['fecha_inicio'] ?? now())->startOfDay();
        $fin = Carbon::parse($filtrosFecha['fecha_fin'] ?? now())->endOfDay();

        $porDia = $this->reporteRepository->getVentasPorDia($inicio, $fin);
        
        return [
            'por_dia' => $porDia,
            'por_camion' => $this->reporteRepository->getVentasPorCamion($inicio, $fin),
            'total_periodo' => $porDia->sum('total')
        ];
    }

    public function getRecaudacion(array $filtrosFecha): array
    {
        $inicio = Carbon::parse($filtrosFecha['fecha_inicio'] ?? now())->startOfDay();
        $fin = Carbon::parse($filtrosFecha['fecha_fin'] ?? now())->endOfDay();

        return [
            'por_metodo_pago' => $this->reporteRepository->getRecaudacionPorMetodo($inicio, $fin)
        ];
    }

    public function getCarritosAbandonados(array $filtrosFecha): Collection
    {
        return collect([]); // TODO
    }

    public function getStock(): array
    {
        return [
            'maestro' => $this->reporteRepository->getStockMaestro(),
            'por_camion' => $this->reporteRepository->getStockPorCamion()
        ];
    }
}
