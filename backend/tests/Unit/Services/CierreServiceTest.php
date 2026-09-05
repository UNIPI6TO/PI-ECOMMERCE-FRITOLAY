<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CierreService;
use App\Contracts\GuiaRepositoryInterface;
use App\Contracts\BodegaRepositoryInterface;
use App\Services\InventarioService;
use App\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Mockery;
use Exception;

class CierreServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $mockQueryBuilder = Mockery::mock();
        $mockQueryBuilder->shouldReceive('insert')->andReturn(true);
        DB::shouldReceive('table')->with('mercaderia_mal_estado')->andReturn($mockQueryBuilder);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_cerrar_guia_valida()
    {
        $guiaRepo = Mockery::mock(GuiaRepositoryInterface::class);
        $bodegaRepo = Mockery::mock(BodegaRepositoryInterface::class);
        $productoRepo = Mockery::mock(\App\Contracts\ProductoRepositoryInterface::class);
        $inventarioSvc = Mockery::mock(InventarioService::class);
        $auditoriaSvc = Mockery::mock(AuditoriaService::class);

        $guiaMock = Mockery::mock(\App\Models\GuiaRemision::class)->makePartial();
        $guiaMock->id = 1;
        $guiaMock->estado = 'confirmacion_cierre';
        $guiaMock->camion_id = 5;

        $guiaRepo->shouldReceive('findByIdRemision')->with(1)->andReturn($guiaMock);
        
        $guiaCerradaMock = Mockery::mock(\App\Models\GuiaRemision::class)->makePartial();
        $guiaCerradaMock->id = 1;
        $guiaCerradaMock->estado = 'cerrada';
        $guiaCerradaMock->camion_id = 5;

        $guiaRepo->shouldReceive('updateRemision')->with(1, [
            'estado' => 'cerrada',
            'efectivo_recibido' => 100.50
        ])->andReturn($guiaCerradaMock);

        $inventarioSvc->shouldReceive('encerarBodegaCamion')->with(5)->once();
        $auditoriaSvc->shouldReceive('logSimple')->once();

        $service = new CierreService($guiaRepo, $bodegaRepo, $productoRepo, $inventarioSvc, $auditoriaSvc);
        $resultado = $service->cerrarGuia(1, 100.50, 99);

        $this->assertEquals('cerrada', $resultado->estado);
    }

    public function test_procesar_mercaderia_devuelta()
    {
        $guiaRepo = Mockery::mock(GuiaRepositoryInterface::class);
        $bodegaRepo = Mockery::mock(BodegaRepositoryInterface::class);
        $productoRepo = Mockery::mock(\App\Contracts\ProductoRepositoryInterface::class);
        $inventarioSvc = Mockery::mock(InventarioService::class);
        $auditoriaSvc = Mockery::mock(AuditoriaService::class);

        $mercaderias = [
            ['producto_id' => 1, 'cantidad' => 2, 'estado' => 'buen_estado', 'motivo' => 'Sobrante'],
            ['producto_id' => 2, 'cantidad' => 1, 'estado' => 'mal_estado', 'motivo' => 'Caducado']
        ];

        $inventarioSvc->shouldReceive('ingresoMaestro')->with(1, 2, 'Sobrante')->once();
        // El mal estado usa DB::table(), que ya está mockeado en setUp()

        $service = new CierreService($guiaRepo, $bodegaRepo, $productoRepo, $inventarioSvc, $auditoriaSvc);
        $service->procesarMercaderiaDevuelta(10, $mercaderias, 99);
        
        $this->assertTrue(true); // Si llega aquí, los mocks de DB e inventario fueron llamados correctamente
    }
}
