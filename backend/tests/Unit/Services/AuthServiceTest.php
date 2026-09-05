<?php declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthServiceTest extends TestCase
{
    public function test_generate_pin()
    {
        $service = $this->app->make(AuthService::class);
        $pin = $service->generatePin(6);
        $this->assertEquals(6, strlen($pin));
        
        $pin4 = $service->generatePin(4);
        $this->assertEquals(4, strlen($pin4));
    }

    public function test_calculate_ttl_remember()
    {
        $service = $this->app->make(AuthService::class);
        $ttl15Days = 15 * 86400; // 1,296,000 segundos

        $this->assertEquals($ttl15Days, $service->calculateTtl('cliente', true));
        $this->assertEquals($ttl15Days, $service->calculateTtl('chofer', true));
        $this->assertEquals($ttl15Days, $service->calculateTtl('administrador', true));

        // Por defecto sin recuérdame
        $this->assertEquals(3600, $service->calculateTtl('cliente', false));
        $this->assertEquals(86400, $service->calculateTtl('chofer', false));
    }
}
