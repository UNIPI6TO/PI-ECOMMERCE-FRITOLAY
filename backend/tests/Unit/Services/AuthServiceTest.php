<?php declare(strict_types=1);

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\AuthService;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_pin()
    {
        $service = $this->app->make(AuthService::class);
        $pin = $service->generatePin(6);
        $this->assertEquals(6, strlen($pin));
        
        $pin4 = $service->generatePin(4);
        $this->assertEquals(4, strlen($pin4));
    }
}
