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

    public function aprobar(int $id, Request $request)
    {
        try {
            $pedido = $this->aprobacionService->aprobar($id, (int) $request->input('user_id'));
            return response()->json(['message' => 'Pedido aprobado', 'data' => $pedido]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function autoAprobarMasivo(Request $request)
    {
        try {
            $pedidos = \App\Models\Pedido::where('estado', 'en_espera_aprobacion')
                ->whereIn('metodo_pago', ['efectivo', 'tc', 'td'])
                ->get();
            
            $count = 0;
            foreach ($pedidos as $p) {
                $this->aprobacionService->aprobar($p->id, (int) $request->input('user_id'));
                $count++;
            }
            return response()->json(['message' => "Se aprobaron $count pedidos automáticamente.", 'count' => $count]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function rechazar(int $id, Request $request)
    {
        $request->validate([
            'motivo' => 'required|string|max:255'
        ]);

        try {
            $pedido = $this->aprobacionService->rechazar($id, (int) $request->input('user_id'), $request->input('motivo'));
            return response()->json(['message' => 'Pedido rechazado', 'data' => $pedido]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
