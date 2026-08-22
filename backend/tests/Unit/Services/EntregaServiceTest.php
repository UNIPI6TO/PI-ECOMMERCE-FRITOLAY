<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\EntregaService;
use App\Contracts\PedidoRepositoryInterface;
use App\Services\InventarioService;
use App\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Mockery;
use Exception;

class EntregaServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_seleccionar_pedido()
    {
        $pedidoRepo = Mockery::mock(PedidoRepositoryInterface::class);
        $inventarioSvc = Mockery::mock(InventarioService::class);
        $auditoriaSvc = Mockery::mock(AuditoriaService::class);

        $pedidoBase = Mockery::mock(\App\Models\Pedido::class)->makePartial();
        $pedidoBase->id = 1;
        $pedidoBase->estado = 'en_ruta';

        $pedidoRepo->shouldReceive('update')->with(1, ['estado' => 'en_ruta'])->andReturn($pedidoBase);

        $service = new EntregaService($pedidoRepo, $inventarioSvc, $auditoriaSvc);
        $resultado = $service->seleccionarPedido(1, 99);

        $this->assertEquals('en_ruta', $resultado->estado);
    }

    public function test_registrar_entrega_tarjeta_con_devolucion_falla()
    {
        $pedidoRepo = Mockery::mock(PedidoRepositoryInterface::class);
        $inventarioSvc = Mockery::mock(InventarioService::class);
        $auditoriaSvc = Mockery::mock(AuditoriaService::class);

        $pedidoBase = Mockery::mock(\App\Models\Pedido::class)->makePartial();
        $pedidoBase->id = 1;
        $pedidoBase->metodo_pago = 'tarjeta';

        $pedidoRepo->shouldReceive('findById')->with(1)->andReturn($pedidoBase);

        $service = new EntregaService($pedidoRepo, $inventarioSvc, $auditoriaSvc);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No se permiten devoluciones en pedidos pagados con tarjeta.');

        $service->registrarEntrega([
            'pedido_id' => 1,
            'items' => [
                ['item_pedido_id' => 10, 'cantidad_devuelta' => 1]
            ]
        ], 99);
    }
}
