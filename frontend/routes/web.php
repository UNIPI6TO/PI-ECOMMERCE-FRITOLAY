<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EcommerceController;
use App\Http\Controllers\AuthFrontController;
use App\Http\Controllers\GestionPedidosController;
use App\Http\Controllers\GestionRutasController;
use App\Http\Controllers\EntregasFrontController;
use App\Http\Controllers\DashboardFrontController;
use App\Http\Controllers\AdminFrontController;

Route::get('/', fn() => redirect('/ecommerce/catalogo'));

Route::prefix('ecommerce')->group(function () {
    Route::get('/catalogo', [EcommerceController::class, 'catalogo']);
    Route::get('/checkout', [EcommerceController::class, 'checkout']);
    Route::get('/pasarela', [EcommerceController::class, 'pasarela']);
    Route::get('/confirmacion', [EcommerceController::class, 'confirmacion']);
    Route::get('/historial', [EcommerceController::class, 'historial']);
    Route::get('/rastreo/{pedidoId}', [EcommerceController::class, 'rastreo']);
});

Route::prefix('auth')->group(function () {
    Route::get('/login', [AuthFrontController::class, 'login']);
    Route::get('/registro', [AuthFrontController::class, 'registro']);
    Route::get('/recover', [AuthFrontController::class, 'recover']);
});

Route::prefix('perfil')->group(function () {
    Route::get('/', fn() => view('auth.perfil'));
    Route::get('/password', fn() => view('auth.password'));
});

Route::prefix('gestion-rutas')->group(function () {
    Route::get('/', [GestionRutasController::class, 'index']);
});

Route::prefix('gestion-pedidos')->group(function () {
    Route::get('/', [GestionPedidosController::class, 'index']);
    Route::get('/aprobacion', [GestionPedidosController::class, 'aprobacion']);
    Route::get('/asignacion', [GestionPedidosController::class, 'asignacion']);
    Route::get('/cierre', [GestionPedidosController::class, 'cierre']);
    Route::get('/facturas', [GestionPedidosController::class, 'facturas']);
    Route::get('/descuentos', [GestionPedidosController::class, 'descuentos']);
});

Route::prefix('entregas')->group(function () {
    Route::get('/', [EntregasFrontController::class, 'guias']);
    Route::get('/mapa/{guiaRutaId}', [EntregasFrontController::class, 'mapaRuta']);
    Route::get('/entregar/{pedidoId}', [EntregasFrontController::class, 'registrarEntrega']);
    Route::get('/cierre-caja', [EntregasFrontController::class, 'cierreCaja']);
});

Route::get('/dashboard', [DashboardFrontController::class, 'index']);

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminFrontController::class, 'index']);
    Route::get('/usuarios', [AdminFrontController::class, 'usuarios']);
    Route::get('/camiones', [AdminFrontController::class, 'camiones']);
});
