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

    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $pedidos = \App\Models\Pedido::with(['cliente.usuario', 'direccion'])->orderBy('id', 'desc')->get();
        
        $data = $pedidos->map(function($p) {
            $estadoStr = strtoupper($p->estado);
            
            if ($estadoStr === 'EN_ESPERA_APROBACION') {
                $estadoStr = 'PENDIENTE';
            } elseif (in_array($estadoStr, ['EN_ESPERA_ASIGNACION', 'LISTO_PARA_ENTREGAR'])) {
                $estadoStr = 'APROBADO';
            } elseif (in_array($estadoStr, ['ENTREGADO', 'ENTREGADO_PARCIALMENTE'])) {
                $estadoStr = 'ENTREGADO';
            }

            return [
                'id' => $p->id,
                'cliente' => $p->cliente ? ($p->cliente->razon_social ?: ($p->cliente->nombre_cliente ?: ($p->cliente->usuario->nombre ?? 'Sin Cliente'))) : 'Desconocido',
                'nombre_persona' => $p->cliente ? ($p->cliente->nombre_cliente ?: ($p->cliente->usuario->nombre ?? 'Desconocido')) : 'Desconocido',
                'pago' => strtoupper($p->metodo_pago),
                'total' => $p->total,
                'estado' => $estadoStr,
                'fecha' => $p->creado_en ? $p->creado_en->format('Y-m-d H:i') : '',
                'raw_fecha' => $p->creado_en ? $p->creado_en->timestamp : 0,
                'lat' => $p->direccion ? $p->direccion->latitud : null,
                'lng' => $p->direccion ? $p->direccion->longitud : null,
            ];
        });

        return response()->json($data);
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
                $mesPedido = $resultado['pedido']->created_at ? $resultado['pedido']->created_at->format('Y-m') : date('Y-m');
                $path = $this->gcsService->subirComprobante($request->file('comprobante'), $resultado['pedido']->id, $cliente->id, $mesPedido);
                $resultado['pedido']->update(['comprobante_path' => $path]);
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
            $pedido = \App\Models\Pedido::findOrFail($id);
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
