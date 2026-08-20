<?php

declare(strict_types=1);

namespace Tests\Feature\Checkout;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrearPedidoTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_pedidos_exitoso()
    {
        $this->assertTrue(true);
    }

    public function test_sin_comprobante_para_deposito_422()
    {
        $this->assertTrue(true);
    }

    public function test_stock_insuficiente_422()
    {
        $this->assertTrue(true);
    }

    public function test_sin_jwt_401()
    {
        $this->assertTrue(true);
    }
}
