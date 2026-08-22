<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AprobacionService;
use App\Contracts\PedidoRepositoryInterface;
use App\Contracts\ProductoRepositoryInterface;
use App\Services\AuditoriaService;
use Mockery;
use Exception;

class AprobacionServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_aprobar_pedido_valido()
    {
        $pedidoRepo = Mockery::mock(PedidoRepositoryInterface::class);
        $productoRepo = Mockery::mock(ProductoRepositoryInterface::class);
        $auditoriaSvc = Mockery::mock(AuditoriaService::class);

        $pedidoBase = Mockery::mock(\App\Models\Pedido::class)->makePartial();
        $pedidoBase->id = 1;
        $pedidoBase->estado = 'en_espera_aprobacion';
        $pedidoBase->shouldReceive('toArray')->andReturn(['id' => 1, 'estado' => 'en_espera_asignacion']);

        $pedidoRepo->shouldReceive('findById')->with(1)->andReturn($pedidoBase);
        $pedidoRepo->shouldReceive('update')->with(1, ['estado' => 'en_espera_asignacion'])->andReturn($pedidoBase);
        $auditoriaSvc->shouldReceive('log')->once();

        $service = new AprobacionService($pedidoRepo, $productoRepo, $auditoriaSvc);
        $resultado = $service->aprobar(1, 99);

        $this->assertEquals('en_espera_asignacion', $resultado['estado']);
    }

    public function test_rechazar_libera_stock()
    {
        $pedidoRepo = Mockery::mock(PedidoRepositoryInterface::class);
        $productoRepo = Mockery::mock(ProductoRepositoryInterface::class);
        $auditoriaSvc = Mockery::mock(AuditoriaService::class);

        $itemMock = Mockery::mock(\App\Models\PedidoItem::class)->makePartial();
        $itemMock->producto_id = 5;
        $itemMock->cantidad = 10;
        
        $pedidoBase = Mockery::mock(\App\Models\Pedido::class)->makePartial();
        $pedidoBase->id = 1;
        $pedidoBase->estado = 'en_espera_aprobacion';
        $pedidoBase->items = [$itemMock];
        $pedidoBase->shouldReceive('toArray')->andReturn(['id' => 1, 'estado' => 'cancelado']);

        $pedidoRepo->shouldReceive('findById')->with(1)->andReturn($pedidoBase);
        $pedidoRepo->shouldReceive('update')->with(1, [
            'estado' => 'cancelado',
            'motivo_cancelacion' => 'Cliente no contesta'
        ])->andReturn($pedidoBase);

        $productoRepo->shouldReceive('liberarEnPedidos')->with(5, 10)->once();
        $auditoriaSvc->shouldReceive('log')->once();

        $service = new AprobacionService($pedidoRepo, $productoRepo, $auditoriaSvc);
        $resultado = $service->rechazar(1, 99, 'Cliente no contesta');

        $this->assertEquals('cancelado', $resultado['estado']);
    }

    public function test_aprobar_invalido_lanza_excepcion()
    {
        $pedidoRepo = Mockery::mock(PedidoRepositoryInterface::class);
        $productoRepo = Mockery::mock(ProductoRepositoryInterface::class);
        $auditoriaSvc = Mockery::mock(AuditoriaService::class);

        $pedidoBase = Mockery::mock(\App\Models\Pedido::class)->makePartial();
        $pedidoBase->id = 1;
        $pedidoBase->estado = 'entregado';

        $pedidoRepo->shouldReceive('findById')->with(1)->andReturn($pedidoBase);

        $service = new AprobacionService($pedidoRepo, $productoRepo, $auditoriaSvc);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('El pedido no estÃ¡ en estado vÃ¡lido para aprobaciÃ³n.');

        $service->aprobar(1, 99);
    }
}
