<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DescuentoRequest;
use App\Services\DescuentoService;
use Illuminate\Http\JsonResponse;

class DescuentoController extends Controller
{
    public function __construct(private readonly DescuentoService $descuentoService)
    {
    }

    public function index(): JsonResponse
    {
        $descuentos = $this->descuentoService->getDescuentosVigentes();
        return response()->json(['data' => $descuentos]);
    }

    public function store(DescuentoRequest $request): JsonResponse
    {
        $descuento = $this->descuentoService->crearDescuento($request->validated());
        return response()->json(['data' => $descuento], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->descuentoService->eliminarDescuento($id);
        if ($deleted) {
            return response()->json(['message' => 'Descuento eliminado']);
        }
        return response()->json(['error' => 'No se pudo eliminar'], 404);
    }
}
