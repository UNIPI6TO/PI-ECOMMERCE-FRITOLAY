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
        // Build camion assignment map: pedido_id => [camion_id, camion_placa]
        $asignaciones = \Illuminate\Support\Facades\DB::table('asignacion_pedido_camion as apc')
            ->join('guias_ruta as gr', 'gr.id', '=', 'apc.guia_ruta_id')
            ->join('guias_remision as grem', 'grem.id', '=', 'gr.guia_remision_id')
            ->join('camiones as c', 'c.id', '=', 'grem.camion_id')
            ->whereIn('apc.estado', ['asignado', 'en_ruta'])
            ->select('apc.pedido_id', 'c.id as camion_id', 'c.placa as camion_placa')
            ->get()
            ->keyBy('pedido_id');

        $query = \App\Models\Pedido::with(['cliente.usuario', 'direccion']);

        if ($request->filled('fecha_inicio')) {
            $inicio = \Carbon\Carbon::parse($request->input('fecha_inicio'))->startOfDay();
            $query->where('creado_en', '>=', $inicio);
        }
        if ($request->filled('fecha_fin')) {
            $fin = \Carbon\Carbon::parse($request->input('fecha_fin'))->endOfDay();
            $query->where('creado_en', '<=', $fin);
        }

        $pedidos = $query->orderBy('id', 'desc')->get();
        
        $data = $pedidos->map(function($p) use ($asignaciones) {
            $rawEstado = strtolower($p->estado);
            $estadoStr = strtoupper($p->estado);
            
            if ($estadoStr === 'EN_ESPERA_APROBACION') {
                $estadoStr = 'PENDIENTE';
            } elseif (in_array($estadoStr, ['EN_ESPERA_ASIGNACION', 'LISTO_PARA_ENTREGAR'])) {
                $estadoStr = 'APROBADO';
            } elseif (in_array($estadoStr, ['ENTREGADO', 'ENTREGADO_PARCIALMENTE'])) {
                $estadoStr = 'ENTREGADO';
            }

            $asignacion = $asignaciones->get($p->id);

            return [
                'id' => $p->id,
                'cliente' => $p->cliente ? ($p->cliente->razon_social ?: ($p->cliente->nombre_cliente ?: ($p->cliente->usuario->nombre ?? 'Sin Cliente'))) : 'Desconocido',
                'nombre_persona' => $p->cliente ? ($p->cliente->nombre_cliente ?: ($p->cliente->usuario->nombre ?? 'Desconocido')) : 'Desconocido',
                'pago' => strtoupper($p->metodo_pago),
                'total' => $p->total,
                'estado' => $estadoStr,
                'raw_estado' => $rawEstado,
                'camion_id' => $asignacion ? $asignacion->camion_id : null,
                'camion_placa' => $asignacion ? $asignacion->camion_placa : null,
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
            $usuarioId = (int) $request->input('user_id');
            $rol = strtolower($request->input('user_rol', ''));
            if (in_array($rol, ['chofer', 'operador', 'admin', 'administrador'])) {
                $pedido = app(\App\Contracts\PedidoRepositoryInterface::class)->findById($id);
                if (!$pedido) throw new \Exception("Pedido no encontrado.");
            } else {
                $cliente = \App\Models\Cliente::where('usuario_id', $usuarioId)->first();
                $clienteId = $cliente ? $cliente->id : 0;
                $pedido = $this->pedidoService->getPedido($id, $clienteId);
            }
            return response()->json(['data' => $pedido]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function comprobante(int $id, \Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $usuarioId = (int) $request->input('user_id');
            $rol = strtolower($request->input('user_rol', ''));
            $pedido = \App\Models\Pedido::findOrFail($id);

            if ($rol === 'cliente') {
                $cliente = \App\Models\Cliente::where('usuario_id', $usuarioId)->first();
                if (!$cliente || $pedido->cliente_id !== $cliente->id) {
                    return response()->json(['error' => 'No tiene permisos para ver este comprobante.'], 403);
                }
            }

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

    public function pendientesAprobacion(\Illuminate\Http\Request $request): JsonResponse
    {
        $pedidos = \App\Models\Pedido::with(['cliente.usuario'])
            ->where('estado', \App\Models\Pedido::ESTADO_EN_ESPERA_APROBACION)
            ->get();

        $data = $pedidos->map(function($p) {
            return [
                'id' => $p->id,
                'cliente' => $p->cliente ? ($p->cliente->razon_social ?: ($p->cliente->usuario->nombre ?? 'Sin Cliente')) : 'Desconocido',
                'metodo' => strtoupper($p->metodo_pago),
                'total' => $p->total,
                'fecha' => $p->creado_en ? $p->creado_en->format('Y-m-d H:i') : ''
            ];
        });

        return response()->json($data);
    }

    public function cancelar(int $id, \Illuminate\Http\Request $request): JsonResponse
    {
        try {
            $motivo = $request->input('motivo') ?: $request->input('motivo_cancelacion');
            $this->pedidoService->cancelarPedido($id, (int) $request->input('user_id'), $motivo);
            return response()->json(['message' => 'Pedido cancelado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
