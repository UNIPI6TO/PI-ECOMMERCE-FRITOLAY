<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CarritoAbandonadoRequest;
use App\Services\CarritoAbandonadoService;
use Illuminate\Http\JsonResponse;

class CarritoAbandonadoController extends Controller
{
    public function __construct(private readonly CarritoAbandonadoService $carritoAbandonadoService)
    {
    }

    public function store(CarritoAbandonadoRequest $request): JsonResponse
    {
        $carrito = $this->carritoAbandonadoService->registrar($request->validated());
        return response()->json(['data' => $carrito], 201);
    }
}
