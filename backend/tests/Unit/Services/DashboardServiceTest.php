<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\DashboardService;
use App\Repositories\ReporteRepository;
use Carbon\Carbon;
use Mockery;

class DashboardServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_kpis_calcula_efectividad()
    {
        $reporteRepo = Mockery::mock(ReporteRepository::class);

        $reporteRepo->shouldReceive('getPedidosCountPorEstado')
            ->andReturn([
                'entregado' => 4,
                'entregado_parcialmente' => 1,
                'cancelado' => 5
            ]);

        $service = new DashboardService($reporteRepo);
        
        $filtros = ['fecha_inicio' => '2023-01-01', 'fecha_fin' => '2023-01-31'];
        $resultado = $service->getKpis($filtros);

        $this->assertEquals(50.0, $resultado['efectividad_general']); // 5 entregados de 10 totales
    }

    public function test_get_ventas_calcula_total()
    {
        $reporteRepo = Mockery::mock(ReporteRepository::class);

        $ventasDiaMock = collect([
            ['fecha' => '2023-01-01', 'total' => 100],
            ['fecha' => '2023-01-02', 'total' => 250]
        ]);

        $reporteRepo->shouldReceive('getVentasPorDia')->andReturn($ventasDiaMock);
        $reporteRepo->shouldReceive('getVentasPorCamion')->andReturn(collect([]));

        $service = new DashboardService($reporteRepo);
        
        $filtros = [];
        $resultado = $service->getVentas($filtros);

        $this->assertEquals(350, $resultado['total_periodo']);
    }
}
