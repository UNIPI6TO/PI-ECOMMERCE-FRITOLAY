<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CarritoAbandonadoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DireccionClienteController;
use App\Http\Controllers\DescuentoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\CierreController;
use App\Http\Controllers\CamionController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\GpsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioAdminController;
use App\Http\Controllers\EmpresaConfigController;

// 🚀 ENDPOINT DE STATUS / INFO (público) 🚀
Route::get('/info', function () {
    return response()->json([
        'status' => 'ok',
        'api_name' => config('app.name', 'Fritolay Backend API'),
        'environment' => config('app.env'),
        'timestamp' => now()->toIso8601String(),
        'db_socket' => env('DB_SOCKET', 'MISSING'),
        'db_host' => env('DB_HOST', 'MISSING'),
    ]);
});

// ─── AUTENTICACIÓN (pública) ─────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/login',          [AuthController::class, 'login']);
    Route::post('/registro',       [AuthController::class, 'registro']);
    Route::post('/recover',        [AuthController::class, 'recover']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('jwt')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
        Route::put('/me',      [AuthController::class, 'updateProfile']);
        Route::put('/me/password', [AuthController::class, 'updatePassword']);
    });
});

// ─── CATÁLOGO (público — no requiere JWT) ────────────────────────────────────
Route::prefix('productos')->group(function () {
    Route::get('/',          [ProductoController::class, 'index']);
    Route::get('/{id}',      [ProductoController::class, 'show']);
    Route::get('/{id}/stock',[ProductoController::class, 'stock']);
});

// Registro de carritos abandonados (sin JWT, puede ser invitado)
Route::post('/carritos-abandonados', [CarritoAbandonadoController::class, 'store']);

// Configuración pública del emisor (para generar facturas en el frontend)
Route::get('/empresa', [EmpresaConfigController::class, 'show']);

// ─── RUTAS PROTEGIDAS POR JWT ─────────────────────────────────────────────────
Route::middleware('jwt')->group(function () {

    // ── Perfil de cliente ────────────────────────────────────────────────────
    Route::prefix('clientes')->group(function () {
        Route::post('/',    [ClienteController::class, 'store']);
        Route::get('/me',   [ClienteController::class, 'me']);
        Route::put('/me',   [ClienteController::class, 'update']);

        // Historial de pedidos del cliente
        Route::get('/{clienteId}/pedidos', [PedidoController::class, 'historial']);

        // CRUD de direcciones
        Route::prefix('/{clienteId}/direcciones')->middleware('role:cliente')->group(function () {
            Route::get('/',           [DireccionClienteController::class, 'index']);
            Route::post('/',          [DireccionClienteController::class, 'store']);
            Route::put('/{id}',       [DireccionClienteController::class, 'update']);
            Route::delete('/{id}',    [DireccionClienteController::class, 'destroy']);
            Route::patch('/{id}/default', [DireccionClienteController::class, 'setDefault']);
        });
    });

    // ── Pedidos ──────────────────────────────────────────────────────────────
    Route::prefix('pedidos')->group(function () {
        // Cliente: crear pedido (checkout)
        Route::post('/', [PedidoController::class, 'store'])->middleware('role:cliente');

        // Operador/Admin + cliente propio
        Route::get('/{id}', [PedidoController::class, 'show']);

        // Cancelar pedido
        Route::patch('/{id}/cancelar', [PedidoController::class, 'cancelar']);

        // Comprobante: solo operador/admin
        Route::get('/{id}/comprobante', [PedidoController::class, 'comprobante'])
            ->middleware('role:operador,administrador');

        // Aprobación/Rechazo de pagos (operador/admin)
        Route::get('/pendientes-aprobacion', [PedidoController::class, 'pendientesAprobacion'])
            ->middleware('role:operador,administrador');
        Route::post('/bulk-aprobar-directos', [PagoController::class, 'autoAprobarMasivo'])
            ->middleware('role:operador,administrador');
        Route::patch('/{id}/aprobar',   [PagoController::class, 'aprobar'])
            ->middleware('role:operador,administrador');
        Route::patch('/{id}/rechazar',  [PagoController::class, 'rechazar'])
            ->middleware('role:operador,administrador');

        // Selección del pedido por chofer (inicia GPS tracking)
        Route::patch('/{id}/seleccionar', [EntregaController::class, 'seleccionarPedido'])
            ->middleware('role:chofer');
    });

    // Listado de pedidos con filtros (operador/admin)
    Route::get('/pedidos', [PedidoController::class, 'index'])
        ->middleware('role:operador,administrador');

    // ── Asignaciones de rutas ────────────────────────────────────────────────
    Route::prefix('asignaciones')->middleware('role:operador,administrador')->group(function () {
        Route::post('/',    [AsignacionController::class, 'store']);
        Route::get('/{id}', [AsignacionController::class, 'show']);
        Route::delete('/',  [AsignacionController::class, 'destroy']);
        Route::post('/cerrar-ruta/{camionId}', [AsignacionController::class, 'cerrarRuta']);
    });

    // ── Camiones ─────────────────────────────────────────────────────────────
    Route::prefix('camiones')->group(function () {
        Route::get('/me', [CamionController::class, 'myCamion'])->middleware('role:chofer');

        Route::get('/', [CamionController::class, 'index'])
            ->middleware('role:operador,administrador');
        Route::post('/', [CamionController::class, 'store'])
            ->middleware('role:administrador');
        Route::put('/{id}', [CamionController::class, 'update'])
            ->middleware('role:administrador');
        Route::patch('/{id}/estado', [CamionController::class, 'updateEstado'])
            ->middleware('role:administrador,operador');
        Route::patch('/{id}/chofer', [CamionController::class, 'asignarChofer'])
            ->middleware('role:administrador');

        // Inventario físico del camión (chofer)
        Route::get('/{id}/inventario', [EntregaController::class, 'inventarioCamion'])
            ->middleware('role:chofer');
    });

    // ── Guías de Remisión ────────────────────────────────────────────────────
    Route::prefix('guias-remision')->middleware('role:operador,administrador')->group(function () {
        Route::get('/pendientes-cierre', [CierreController::class, 'pendientesCierre']);
        Route::get('/{id}/detalle',      [CierreController::class, 'detalle']);
        Route::patch('/{id}/cerrar',     [CierreController::class, 'cerrar']);
    });

    // ── Guías de Ruta ────────────────────────────────────────────────────────
    Route::prefix('guias-ruta')->group(function () {
        // Chofer: ver sus guías activas
        Route::get('/', [EntregaController::class, 'misGuias'])
            ->middleware('role:chofer');
        // Chofer: arqueo de caja
                Route::get('/{id}/pedidos', [EntregaController::class, 'getPedidosGuia'])->middleware('role:chofer');
        Route::post('/{id}/arqueo', [CierreController::class, 'declararArqueo'])
            ->middleware('role:chofer');
        // Operador: resumen de caja para cierre
        Route::get('/{id}/resumen-caja', [CierreController::class, 'resumenCaja'])
            ->middleware('role:operador,administrador');
    });

    // ── Entregas ─────────────────────────────────────────────────────────────
    Route::post('/entregas', [EntregaController::class, 'registrarEntrega'])
        ->middleware('role:chofer');

    // ── Inventario ───────────────────────────────────────────────────────────
    Route::post('/inventario/ingreso', [CierreController::class, 'ingresoInventario'])
        ->middleware('role:operador,administrador');

    // ── Mercadería en mal estado ─────────────────────────────────────────────
    Route::post('/mercaderia-mal-estado', [CierreController::class, 'registrarMalEstado'])
        ->middleware('role:operador,administrador');

    // ── Facturas ─────────────────────────────────────────────────────────────
    Route::prefix('facturas')->middleware('role:operador,administrador')->group(function () {
        Route::get('/',    [FacturaController::class, 'index']);
        Route::get('/{id}',[FacturaController::class, 'show']);
    });

    // ── GPS ──────────────────────────────────────────────────────────────────
    Route::post('/gps/ubicacion', [GpsController::class, 'actualizarUbicacion'])
        ->middleware('role:chofer');

    // ── Dashboard (solo administrador y operador) ────────────────────────────
    Route::prefix('dashboard')->middleware('role:administrador,operador,admin')->group(function () {
        Route::get('/kpis',                  [DashboardController::class, 'kpis']);
        Route::get('/ventas',                [DashboardController::class, 'ventas']);
        Route::get('/recaudacion',           [DashboardController::class, 'recaudacion']);
        Route::get('/carritos-abandonados',  [DashboardController::class, 'carritosAbandonados']);
        Route::get('/stock',                 [DashboardController::class, 'stock']);
    });

    // ── Descuentos (solo administrador) ─────────────────────────────────────
    Route::prefix('admin/descuentos')->middleware('role:administrador')->group(function () {
        Route::get('/',    [DescuentoController::class, 'index']);
        Route::post('/',   [DescuentoController::class, 'store']);
        Route::delete('/{id}', [DescuentoController::class, 'destroy']);
    });

    // ── Administración de usuarios (solo administrador) ──────────────────────
    Route::prefix('admin/usuarios')->middleware('role:administrador')->group(function () {
        Route::get('/',                          [UsuarioAdminController::class, 'index']);
        Route::post('/',                         [UsuarioAdminController::class, 'store']);
        Route::put('/{id}',                      [UsuarioAdminController::class, 'update']);
        Route::patch('/{id}/inactivar',          [UsuarioAdminController::class, 'inactivar']);
        Route::patch('/{id}/activar',            [UsuarioAdminController::class, 'activar']);
        Route::patch('/{id}/resetear-password',  [UsuarioAdminController::class, 'resetearPassword']);
    });
});
