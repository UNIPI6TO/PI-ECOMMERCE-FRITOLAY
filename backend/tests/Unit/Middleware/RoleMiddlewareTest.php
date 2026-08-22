<?php declare(strict_types=1);

namespace Tests\Unit\Middleware;

use Tests\TestCase;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleMiddlewareTest extends TestCase
{
    public function test_rol_no_permitido()
    {
        $request = Request::create('/api/test', 'GET');
        $request->merge(['user_rol' => 'cliente']);
        
        $middleware = new RoleMiddleware();
        
        $response = $middleware->handle($request, function () {
            return new Response();
        }, 'administrador');

        $this->assertEquals(403, $response->getStatusCode());
    }

    public function test_rol_permitido()
    {
        $request = Request::create('/api/test', 'GET');
        $request->merge(['user_rol' => 'administrador']);
        
        $middleware = new RoleMiddleware();
        
        $response = $middleware->handle($request, function () {
            return new Response(200);
        }, 'administrador');

        $this->assertEquals(200, $response->getStatusCode());
    }
}
