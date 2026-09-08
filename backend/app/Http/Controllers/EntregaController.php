<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EntregaRequest;
use App\Services\EntregaService;
use Exception;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    public function __construct(
        private readonly EntregaService $entregaService
    ) {}

        public function getPedidosGuia(int $id)
    {
        return response()->json($this->entregaService->getPedidosGuiaChofer($id));
    }

    public function misGuias(Request $request)
    {
        $userId = (int) ($request->user_id ?? $request->query('user_id') ?? 0);
        return response()->json($this->entregaService->getGuiasChofer($userId));
    }

    public function inventarioCamion(int $id)
    {
        return response()->json($this->entregaService->getInventarioCamion($id));
    }

    public function seleccionarPedido(int $id)
    {
        try {
            $pedido = $this->entregaService->seleccionarPedido($id, (int)request('user_id'));
            return response()->json(['message' => 'Pedido seleccionado', 'data' => $pedido]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function registrarEntrega(EntregaRequest $request)
    {
        try {
            $resultado = $this->entregaService->registrarEntrega($request->validated(), (int)request('user_id'));
            return response()->json(['message' => 'Entrega registrada', 'data' => $resultado], 201);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error registrando entrega: " . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function estadoFase(Request $request)
    {
        $userId = (int) ($request->user_id ?? $request->query('user_id') ?? 0);
        return response()->json($this->entregaService->getFaseEstadoChofer($userId));
    }
}
