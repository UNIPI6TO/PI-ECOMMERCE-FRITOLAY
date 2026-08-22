<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Services\PedidoService;
use App\Services\GcsService;
use Illuminate\Http\JsonResponse;

class PedidoController extends Controller
{
    public function __construct(
        private readonly PedidoService $pedidoService,
        private readonly GcsService $gcsService
    ) {
    }

    public function store(CheckoutRequest $request): JsonResponse
    {
        $data = $request->validated();
        $usuarioId = (int) $request->input('user_id');
        $cliente = \App\Models\Cliente::where('usuario_id', $usuarioId)->first();
        if (!$cliente) {
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        try {
            $resultado = $this->pedidoService->crearPedido($data, $cliente->id, $usuarioId);
            
            if ($request->hasFile('comprobante')) {
                $path = $this->gcsService->subirComprobante($request->file('comprobante'), $resultado['pedido']->id);
                // Aquí se debería actualizar el pedido con el path del comprobante.
                // $resultado['pedido']->update(['comprobante_path' => $path]);
            }

            return response()->json(['data' => $resultado], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function show(int $id, \Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $pedido = $this->pedidoService->getPedido($id, (int) $request->input('user_id'));
            return response()->json(['data' => $pedido]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function comprobante(int $id, \Illuminate\Http\Request $request): JsonResponse
    {
        try {
            // Se asume que getPedido() sin $clienteId en caso de ser admin
            $pedido = $this->pedidoService->getPedido($id, (int) $request->input('user_id')); // Ajustar lógica según roles si es necesario
            $url = $this->gcsService->getUrlFirmada($pedido->comprobante_path ?? '');
            return response()->json(['data' => ['url' => $url]]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function historial(int $clienteId, \Illuminate\Http\Request $request): JsonResponse
    {
        $cliente = \App\Models\Cliente::find($clienteId);
        if (!$cliente || (int) $request->input('user_id') !== $cliente->usuario_id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        $pedidos = $this->pedidoService->getHistorial($clienteId);
        return response()->json(['data' => $pedidos]);
    }

    public function cancelar(int $id, \Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $this->pedidoService->cancelarPedido($id, (int) $request->input('user_id'));
            return response()->json(['message' => 'Pedido cancelado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
