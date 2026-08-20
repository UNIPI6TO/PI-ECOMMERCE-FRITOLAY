<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class EcommerceController extends Controller
{
    public function catalogo(): View
    {
        return view('ecommerce.catalogo');
    }

    public function checkout(): View
    {
        return view('ecommerce.checkout');
    }

    public function pasarela(): View
    {
        return view('ecommerce.pasarela');
    }

    public function confirmacion(): View
    {
        return view('ecommerce.confirmacion');
    }

    public function historial(): View
    {
        return view('ecommerce.historial');
    }

    public function rastreo(string $pedidoId): View
    {
        return view('ecommerce.rastreo', compact('pedidoId'));
    }
}
