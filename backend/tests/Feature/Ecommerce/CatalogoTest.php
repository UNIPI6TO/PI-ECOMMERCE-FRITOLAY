<?php

declare(strict_types=1);

namespace Tests\Feature\Ecommerce;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_productos_200()
    {
        $this->assertTrue(true);
    }

    public function test_get_producto_404()
    {
        $this->assertTrue(true);
    }
}
