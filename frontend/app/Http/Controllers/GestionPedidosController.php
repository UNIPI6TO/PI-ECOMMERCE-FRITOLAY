<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class GestionPedidosController extends Controller
{
    public function index(): View
    {
        return view('gestion-pedidos.index');
    }

    public function aprobacion(): View
    {
        return view('gestion-pedidos.aprobacion');
    }

    public function asignacion(): View
    {
        return view('gestion-pedidos.asignacion');
    }

    public function cierre(): View
    {
        return view('gestion-pedidos.cierre');
    }

    public function facturas(): View
    {
        return view('gestion-pedidos.facturas');
    }

    public function descuentos(): View
    {
        return view('gestion-pedidos.descuentos');
    }
}
