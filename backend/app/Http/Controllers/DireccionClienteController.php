<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DireccionClienteRequest;
use App\Services\DireccionClienteService;
use Illuminate\Http\JsonResponse;

class DireccionClienteController extends Controller
{
    public function __construct(private readonly DireccionClienteService $direccionService)
    {
    }

    public function store(DireccionClienteRequest $request): JsonResponse
    {
        $direccion = $this->direccionService->create(auth()->id(), $request->validated());
        return response()->json(['data' => $direccion], 201);
    }

    public function update(DireccionClienteRequest $request, int $id): JsonResponse
    {
        $success = $this->direccionService->update($id, $request->validated());
        return response()->json(['success' => $success]);
    }

    public function destroy(int $id): JsonResponse
    {
        $success = $this->direccionService->delete($id);
        return response()->json(['success' => $success]);
    }

    public function setDefault(int $clienteId, int $id): JsonResponse
    {
        if (auth()->id() !== $clienteId) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $this->direccionService->setDefault($clienteId, $id);
        return response()->json(['success' => true]);
    }
}
