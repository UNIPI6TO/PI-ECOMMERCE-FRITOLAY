<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\RutaService;
use App\Contracts\PedidoRepositoryInterface;
use App\Contracts\CamionRepositoryInterface;
use App\Contracts\GuiaRepositoryInterface;
use App\Contracts\BodegaRepositoryInterface;
use App\Services\InventarioService;
use App\Services\AuditoriaService;
use Mockery;
use Exception;

class RutaServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_crear_asignacion_con_camion_inactivo_falla()
    {
        $pedidoRepo = Mockery::mock(PedidoRepositoryInterface::class);
        $camionRepo = Mockery::mock(CamionRepositoryInterface::class);
        $guiaRepo = Mockery::mock(GuiaRepositoryInterface::class);
        $bodegaRepo = Mockery::mock(BodegaRepositoryInterface::class);
        $inventarioSvc = Mockery::mock(InventarioService::class);
        $auditoriaSvc = Mockery::mock(AuditoriaService::class);

        $camionMock = Mockery::mock(\App\Models\Camion::class)->makePartial();
        $camionMock->id = 5;
        $camionMock->estado = 'inactivo';
        $camionRepo->shouldReceive("findById")->with(5)->andReturn($camionMock);

        $service = new RutaService($pedidoRepo, $camionRepo, $guiaRepo, $bodegaRepo, $inventarioSvc, $auditoriaSvc);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("El camiÃ³n no estÃ¡ activo.");

        $service->crearAsignacion([1, 2], 5, 99);
    }
}
