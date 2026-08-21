<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\DescuentoService;
use App\Models\Descuento;
use Mockery;

class DescuentoServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_calcular_descuento_toma_el_maximo()
    {
        $builderMock = Mockery::mock();
        $builderMock->shouldReceive('where')->andReturnSelf();
        $builderMock->shouldReceive('whereIn')->andReturnSelf();
        $builderMock->shouldReceive('orderByDesc')->andReturnSelf();
        
        // Simular que el descuento individual es 10% y el global es 15%
        // Debido a que calcularDescuento llama a Descuento::where dos veces (una para individual y otra para global)
        $builderMock->shouldReceive('first')->andReturnValues([
            (object)['porcentaje' => 10], // Individual
            (object)['porcentaje' => 15]  // Global
        ]);

        $descuentoMock = Mockery::mock('alias:App\Models\Descuento');
        $descuentoMock->shouldReceive('where')->andReturn($builderMock);

        $service = new DescuentoService();
        $resultado = $service->calcularDescuento(1, 'efectivo', 1000);

        // 1000 * 15% = 150
        $this->assertEquals(150.0, $resultado);
    }

}
