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
        $usuarioId = auth()->id();

        try {
            $resultado = $this->pedidoService->crearPedido($data, $usuarioId);
            
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

    public function show(int $id): JsonResponse
    {
        try {
            $pedido = $this->pedidoService->getPedido($id, auth()->id());
            return response()->json(['data' => $pedido]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function comprobante(int $id): JsonResponse
    {
        try {
            // Se asume que getPedido() sin $clienteId en caso de ser admin
            $pedido = $this->pedidoService->getPedido($id, auth()->id()); // Ajustar lógica según roles si es necesario
            $url = $this->gcsService->getUrlFirmada($pedido->comprobante_path ?? '');
            return response()->json(['data' => ['url' => $url]]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function historial(int $clienteId): JsonResponse
    {
        if (auth()->id() !== $clienteId) {
            return response()->json(['error' => 'No autorizado'], 403);
        }
        
        $pedidos = $this->pedidoService->getHistorial($clienteId);
        return response()->json(['data' => $pedidos]);
    }
}
