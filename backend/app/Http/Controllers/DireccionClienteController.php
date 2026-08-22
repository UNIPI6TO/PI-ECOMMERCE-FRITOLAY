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

    public function index(int $clienteId): JsonResponse
    {
        $direcciones = \App\Models\DireccionCliente::where('cliente_id', $clienteId)->get();
        return response()->json($direcciones);
    }

    public function store(DireccionClienteRequest $request, int $clienteId): JsonResponse
    {
        $direccion = $this->direccionService->create($clienteId, $request->validated());
        return response()->json(['data' => $direccion], 201);
    }

    public function update(DireccionClienteRequest $request, int $clienteId, int $id): JsonResponse
    {
        $success = $this->direccionService->update($id, $request->validated());
        return response()->json(['success' => $success]);
    }

    public function destroy(int $clienteId, int $id): JsonResponse
    {
        $success = $this->direccionService->delete($id);
        return response()->json(['success' => $success]);
    }

    public function setDefault(\Illuminate\Http\Request $request, int $clienteId, int $id): JsonResponse
    {
        // Actually, user_id from token should be the same as the cliente's user, but let's assume it's correct for now or validate it using the Cliente model
        $cliente = \App\Models\Cliente::find($clienteId);
        if (!$cliente || $cliente->usuario_id !== $request->input('user_id')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        $this->direccionService->setDefault($clienteId, $id);
        return response()->json(['success' => true]);
    }
}
