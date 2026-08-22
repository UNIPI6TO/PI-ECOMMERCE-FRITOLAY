<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\InventarioService;
use App\Contracts\ProductoRepositoryInterface;
use App\Contracts\BodegaRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Mockery;

class InventarioServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::shouldReceive('transaction')
            ->andReturnUsing(function ($closure) {
                return $closure();
            });
            
        // Mock the DB::table('transacciones')->insert(...)
        $mockQueryBuilder = Mockery::mock();
        $mockQueryBuilder->shouldReceive('insert')->andReturn(true);
        DB::shouldReceive('table')->with('transacciones')->andReturn($mockQueryBuilder);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_incrementar_en_pedidos()
    {
        $prodRepo = Mockery::mock(ProductoRepositoryInterface::class);
        $bodegaRepo = Mockery::mock(BodegaRepositoryInterface::class);

        $productoMock = Mockery::mock(\App\Models\Producto::class)->makePartial();
        $productoMock->id = 1;
        $productoMock->en_pedidos = 10;
        $prodRepo->shouldReceive('findById')->with(1)->andReturn($productoMock);
        
        // Verifica que se guarde el nuevo valor: 10 + 5.5 = 15.5
        $prodRepo->shouldReceive('update')->with(1, ['en_pedidos' => 15.5])->once();

        $service = new InventarioService($prodRepo, $bodegaRepo);
        $service->incrementarEnPedidos(1, 5.5);
    }

    public function test_egreso_fisico_camion()
    {
        $prodRepo = Mockery::mock(ProductoRepositoryInterface::class);
        $bodegaRepo = Mockery::mock(BodegaRepositoryInterface::class);

        $bodegaRepo->shouldReceive('decrement')->with(99, 1, 10.0)->once();

        $productoMock = Mockery::mock(\App\Models\Producto::class)->makePartial();
        $productoMock->id = 1;
        $productoMock->en_pedidos = 20;
        $prodRepo->shouldReceive('findById')->with(1)->andReturn($productoMock);
        
        // El decrementar bajarǭ de 20 a 10 (porque se restan 10)
        $prodRepo->shouldReceive('update')->with(1, ['en_pedidos' => 10.0])->once();

        $service = new InventarioService($prodRepo, $bodegaRepo);
        $service->egresoFisicoCamion(99, 1, 10.0);
    }
}
