<?php declare(strict_types=1);

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RecoverPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Usuario::create([
            'nombre' => 'Test User',
            'email' => 'test@test.com',
            'password_hash' => Hash::make('password123'),
            'rol' => 'cliente',
            'activo' => true
        ]);
    }

    public function test_recover_email_existente()
    {
        $response = $this->postJson('/api/auth/recover', [
            'email' => 'test@test.com'
        ]);

        $response->assertStatus(200);
    }

    public function test_recover_email_inexistente()
    {
        $response = $this->postJson('/api/auth/recover', [
            'email' => 'none@test.com'
        ]);

        $response->assertStatus(404);
    }

    public function test_recover_email_invalido()
    {
        $response = $this->postJson('/api/auth/recover', [
            'email' => 'invalid-email'
        ]);

        $response->assertStatus(422);
    }
}
