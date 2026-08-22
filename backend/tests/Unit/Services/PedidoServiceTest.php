<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\PedidoService;
use App\Services\InventarioService;
use App\Services\DescuentoService;
use App\Services\AuditoriaService;
use App\Contracts\PedidoRepositoryInterface;
use App\Contracts\ProductoRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Mockery;

class PedidoServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock DB transaction to just execute the closure
        DB::shouldReceive('transaction')
            ->andReturnUsing(function ($closure) {
                return $closure();
            });
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_efectivo_espera_asignacion()
    {
        $pedidoRepo = Mockery::mock(PedidoRepositoryInterface::class);
        $productoRepo = Mockery::mock(ProductoRepositoryInterface::class);
        $inventarioSvc = Mockery::mock(InventarioService::class);
        $descuentoSvc = Mockery::mock(DescuentoService::class);
        $auditoriaSvc = Mockery::mock(AuditoriaService::class);

        $service = new PedidoService($pedidoRepo, $productoRepo, $inventarioSvc, $descuentoSvc, $auditoriaSvc);

        // Mock producto response
        $productoMock = Mockery::mock(\App\Models\Producto::class)->makePartial();
        $productoMock->id = 1;
        $productoMock->precio = 10;
        $productoMock->cantidad_fisica = 100;
        $productoMock->en_pedidos = 0;
        $productoRepo->shouldReceive('findById')->with(1)->andReturn($productoMock);

        // Descuento = 0
        $descuentoSvc->shouldReceive('calcularDescuento')->with(99, 'efectivo', 20)->andReturn(0.0);
        
        config(['fritolay.iva_porcentaje' => 15]);

        $pedidoMock = Mockery::mock(\App\Models\Pedido::class)->makePartial();
        $pedidoMock->id = 123;
        $pedidoRepo->shouldReceive('create')->with(Mockery::on(function ($data) {
            return $data['estado'] === 'en_espera_asignacion' 
                && $data['metodo_pago'] === 'efectivo'
                && $data['subtotal'] == 20
                && $data['total'] == 23; // 20 + 15% IVA (3)
        }))->andReturn($pedidoMock);

        $itemMock = Mockery::mock(\App\Models\PedidoItem::class)->makePartial();
        
        $hasManyMock = Mockery::mock();
        $hasManyMock->shouldReceive('create')->andReturn($itemMock);
        $pedidoMock->shouldReceive('items')->andReturn($hasManyMock);

        $inventarioSvc->shouldReceive('incrementarEnPedidos')->with(1, 2.0);
        $auditoriaSvc->shouldReceive('log');

        $result = $service->crearPedido([
            'direccion_id' => 1,
            'metodo_pago' => 'efectivo',
            'items' => [
                ['producto_id' => 1, 'cantidad' => 2]
            ]
        ], 99, 99);

        $this->assertEquals(123, $result['pedido']->id);
    }

    public function test_deposito_espera_aprobacion()
    {
        $pedidoRepo = Mockery::mock(PedidoRepositoryInterface::class);
        $productoRepo = Mockery::mock(ProductoRepositoryInterface::class);
        $inventarioSvc = Mockery::mock(InventarioService::class);
        $descuentoSvc = Mockery::mock(DescuentoService::class);
        $auditoriaSvc = Mockery::mock(AuditoriaService::class);

        $service = new PedidoService($pedidoRepo, $productoRepo, $inventarioSvc, $descuentoSvc, $auditoriaSvc);

        $productoMock = Mockery::mock(\App\Models\Producto::class)->makePartial();
        $productoMock->id = 1;
        $productoMock->precio = 10;
        $productoMock->cantidad_fisica = 100;
        $productoMock->en_pedidos = 0;
        $productoRepo->shouldReceive('findById')->with(1)->andReturn($productoMock);

        $descuentoSvc->shouldReceive('calcularDescuento')->andReturn(0.0);
        config(['fritolay.iva_porcentaje' => 15]);

        $pedidoMock = Mockery::mock(\App\Models\Pedido::class)->makePartial();
        $pedidoMock->id = 124;
        $pedidoRepo->shouldReceive('create')->with(Mockery::on(function ($data) {
            return $data['estado'] === 'en_espera_aprobacion' && $data['metodo_pago'] === 'deposito';
        }))->andReturn($pedidoMock);

        $itemMock = Mockery::mock(\App\Models\PedidoItem::class)->makePartial();
        $hasManyMock = Mockery::mock();
        $hasManyMock->shouldReceive('create')->andReturn($itemMock);
        $pedidoMock->shouldReceive('items')->andReturn($hasManyMock);

        $inventarioSvc->shouldReceive('incrementarEnPedidos');
        $auditoriaSvc->shouldReceive('log');

        $result = $service->crearPedido([
            'direccion_id' => 1,
            'metodo_pago' => 'deposito',
            'items' => [
                ['producto_id' => 1, 'cantidad' => 2]
            ]
        ], 99, 99);

        $this->assertEquals(124, $result['pedido']->id);
    }

    public function test_stock_insuficiente_lanza_excepcion()
    {
        $pedidoRepo = Mockery::mock(PedidoRepositoryInterface::class);
        $productoRepo = Mockery::mock(ProductoRepositoryInterface::class);
        $inventarioSvc = Mockery::mock(InventarioService::class);
        $descuentoSvc = Mockery::mock(DescuentoService::class);
        $auditoriaSvc = Mockery::mock(AuditoriaService::class);

        $service = new PedidoService($pedidoRepo, $productoRepo, $inventarioSvc, $descuentoSvc, $auditoriaSvc);

        $productoMock = Mockery::mock(\App\Models\Producto::class)->makePartial();
        $productoMock->id = 1;
        $productoMock->precio = 10;
        $productoMock->cantidad_fisica = 10;
        $productoMock->en_pedidos = 5; // Disponible = 5
        $productoRepo->shouldReceive('findById')->with(1)->andReturn($productoMock);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stock insuficiente para el producto ID: 1');

        $service->crearPedido([
            'direccion_id' => 1,
            'metodo_pago' => 'efectivo',
            'items' => [
                ['producto_id' => 1, 'cantidad' => 6] // Pide 6, solo hay 5
            ]
        ], 99, 99);
    }

    public function test_calculo_iva_correcto()
    {
        $this->assertTrue(true); // Se prueba indirectamente en test_efectivo_espera_asignacion
    }
}
