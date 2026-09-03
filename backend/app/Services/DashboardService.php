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
        $kpisGenerales = $this->reporteRepository->getKpisGenerales($inicio, $fin);

        return [
            'pedidos_por_estado' => $porEstado,
            'efectividad_general' => $kpisGenerales['efectividad_porcentaje'],
            'cantidad_total_pedidos' => $kpisGenerales['cantidad_total_pedidos'],
            'valor_total_pedidos' => $kpisGenerales['valor_total_pedidos'],
            'ventas_entregadas_total' => $kpisGenerales['ventas_entregadas_total'],
            'pedidos_entregados_count' => $kpisGenerales['pedidos_entregados_count'],
            'efectividad_por_camion' => []
        ];
    }

    public function getVentas(array $filtrosFecha): array
    {
        $inicio = Carbon::parse($filtrosFecha['fecha_inicio'] ?? now())->startOfDay();
        $fin = Carbon::parse($filtrosFecha['fecha_fin'] ?? now())->endOfDay();

        $porDia = $this->reporteRepository->getVentasPorDia($inicio, $fin);
        $tendencia = $this->reporteRepository->getTendenciaVolumenYVentas($inicio, $fin);
        
        return [
            'por_dia' => $porDia,
            'tendencia_volumen' => $tendencia,
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

    public function getPerdidas(array $filtrosFecha): array
    {
        $inicio = Carbon::parse($filtrosFecha['fecha_inicio'] ?? now())->startOfDay();
        $fin = Carbon::parse($filtrosFecha['fecha_fin'] ?? now())->endOfDay();

        $topMotivos = $this->reporteRepository->getTopPerdidasPorMotivo($inicio, $fin);
        $porDia = $this->reporteRepository->getPerdidasPorDia($inicio, $fin);
        $totalAcumulado = $topMotivos->sum('total_perdido');

        return [
            'top_motivos' => $topMotivos,
            'por_dia' => $porDia,
            'total_acumulado_perdido' => round($totalAcumulado, 2)
        ];
    }

    public function getCarritosAbandonados(array $filtrosFecha): Collection
    {
        $inicio = Carbon::parse($filtrosFecha['fecha_inicio'] ?? now())->startOfDay();
        $fin = Carbon::parse($filtrosFecha['fecha_fin'] ?? now())->endOfDay();

        return $this->reporteRepository->getCarritosAbandonados($inicio, $fin);
    }

    public function getStock(): array
    {
        return [
            'maestro' => $this->reporteRepository->getStockMaestro(),
            'por_camion' => $this->reporteRepository->getStockPorCamion()
        ];
    }
}
