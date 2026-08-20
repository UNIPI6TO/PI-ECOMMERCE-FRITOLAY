<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([]);
    }

    public function show(int $id)
    {
        return response()->json([]);
    }
}
