<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminFrontController extends Controller
{
    public function index(): View
    {
        return view('admin.index');
    }

    public function usuarios(): View
    {
        return view('admin.usuarios');
    }

    public function camiones(): View
    {
        return view('admin.camiones');
    }
}
