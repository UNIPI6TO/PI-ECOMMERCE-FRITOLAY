<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class EntregasFrontController extends Controller
{
    public function guias(): View
    {
        return view('entregas.guias');
    }

    public function mapaRuta(int $guiaRutaId): View
    {
        return view('entregas.mapa-ruta', compact('guiaRutaId'));
    }

    public function registrarEntrega(int $pedidoId): View
    {
        return view('entregas.registrar-entrega', compact('pedidoId'));
    }

    public function cierreCaja(): View
    {
        return view('entregas.cierre-caja');
    }
}
