<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class AuthFrontController extends Controller
{
    public function login(): View
    {
        return view('auth.login');
    }

    public function recover(): View
    {
        return view('auth.recover');
    }
}
