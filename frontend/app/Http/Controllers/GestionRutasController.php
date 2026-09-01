<?php
namespace App\Http\Controllers;
use Illuminate\View\View;

class GestionRutasController extends Controller
{
    public function index(): View
    {
        return view('gestion-rutas.index');
    }
}
