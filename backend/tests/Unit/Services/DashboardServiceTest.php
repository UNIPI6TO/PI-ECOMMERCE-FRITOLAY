<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\DashboardService;
use App\Repositories\ReporteRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardServiceTest extends TestCase
{
    public function test_get_kpis_calcula_efectividad()
    {
        $reporteRepo = new class extends ReporteRepository {
            public function getPedidosCountPorEstado(Carbon $inicio, Carbon $fin): array
            {
                return [
                    'entregado' => 4,
                    'entregado_parcialmente' => 1,
                    'cancelado' => 5
                ];
            }
        };

        $service = new DashboardService($reporteRepo);
        
        $filtros = ['fecha_inicio' => '2023-01-01', 'fecha_fin' => '2023-01-31'];
        $resultado = $service->getKpis($filtros);

        $this->assertEquals(50.0, $resultado['efectividad_general']); // 5 entregados de 10 totales
    }

    public function test_get_ventas_calcula_total()
    {
        $reporteRepo = new class extends ReporteRepository {
            public function getVentasPorDia(Carbon $inicio, Carbon $fin): Collection
            {
                return collect([
                    (object)['fecha' => '2023-01-01', 'total' => 100],
                    (object)['fecha' => '2023-01-02', 'total' => 250]
                ]);
            }
            public function getVentasPorCamion(Carbon $inicio, Carbon $fin): Collection
            {
                return collect([]);
            }
        };

        $service = new DashboardService($reporteRepo);
        
        $filtros = [];
        $resultado = $service->getVentas($filtros);

        $this->assertEquals(350, $resultado['total_periodo']);
    }
}
