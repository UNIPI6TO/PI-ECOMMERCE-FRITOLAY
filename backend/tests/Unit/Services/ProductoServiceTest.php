<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ProductoService;
use App\Contracts\ProductoRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Mockery;

class ProductoServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_producto_calcula_stock()
    {
        $repo = Mockery::mock(ProductoRepositoryInterface::class);
        $productoMock = Mockery::mock(\App\Models\Producto::class)->makePartial();
        $productoMock->id = 1;
        $productoMock->cantidad_fisica = 100;
        $productoMock->en_pedidos = 85;
        $productoMock->shouldReceive('toArray')->andReturn([
            "id" => 1,
            "cantidad_fisica" => 100,
            "en_pedidos" => 85
        ]);

        $repo->shouldReceive("findById")->with(1)->andReturn($productoMock);

        $service = new ProductoService($repo);
        $resultado = $service->getProducto(1);

        $this->assertEquals(15, $resultado["disponible"]); 
        $this->assertTrue($resultado["bajo_stock"]); 
        $this->assertFalse($resultado["sin_stock"]); 
    }

    public function test_get_producto_falla_si_no_existe()
    {
        $repo = Mockery::mock(ProductoRepositoryInterface::class);
        $repo->shouldReceive("findById")->with(99)->andReturn(null);

        $service = new ProductoService($repo);

        $this->expectException(ModelNotFoundException::class);
        $service->getProducto(99);
    }
}
