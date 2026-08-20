<?php declare(strict_types=1);

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Usuario::create([
            'nombre' => 'Test User',
            'email' => 'test@test.com',
            'password_hash' => Hash::make('password123'),
            'rol' => 'administrador',
            'activo' => true
        ]);
        
        Usuario::create([
            'nombre' => 'Inactive User',
            'email' => 'inactive@test.com',
            'password_hash' => Hash::make('password123'),
            'rol' => 'cliente',
            'activo' => false
        ]);
    }

    public function test_login_exitoso()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@test.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token']);
    }

    public function test_login_password_incorrecto()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@test.com',
            'password' => 'wrong'
        ]);

        $response->assertStatus(401);
    }

    public function test_login_email_inexistente()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'none@test.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(401);
    }

    public function test_login_usuario_inactivo()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'inactive@test.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(403);
    }

    public function test_login_sin_email()
    {
        $response = $this->postJson('/api/auth/login', [
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
    }
}
