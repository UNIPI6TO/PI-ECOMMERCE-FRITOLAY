<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AprobacionService;
use Exception;

class PagoController extends Controller
{
    public function __construct(
        private readonly AprobacionService $aprobacionService
    ) {}

    public function aprobar(int $id)
    {
        try {
            $pedido = $this->aprobacionService->aprobar($id, auth()->id());
            return response()->json(['message' => 'Pedido aprobado', 'data' => $pedido]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function rechazar(int $id, Request $request)
    {
        $request->validate([
            'motivo' => 'required|string|max:255'
        ]);

        try {
            $pedido = $this->aprobacionService->rechazar($id, auth()->id(), $request->input('motivo'));
            return response()->json(['message' => 'Pedido rechazado', 'data' => $pedido]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
