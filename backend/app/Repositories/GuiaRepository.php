<?php declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\GuiaRepositoryInterface;
use App\Models\GuiaRemision;
use App\Models\GuiaRuta;
use App\Models\AsignacionPedidoCamion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GuiaRepository implements GuiaRepositoryInterface
{
    public function createRemision(array $data): GuiaRemision
    {
        return GuiaRemision::create($data);
    }

    public function createRuta(array $data): GuiaRuta
    {
        return GuiaRuta::create($data);
    }

    public function createAsignacion(array $data): AsignacionPedidoCamion
    {
        return AsignacionPedidoCamion::create($data);
    }

    public function findRemisionById(int $id): ?GuiaRemision
    {
        return GuiaRemision::with(['camion', 'operador', 'guiasRuta'])->find($id);
    }

    public function findRutaById(int $id): ?GuiaRuta
    {
        return GuiaRuta::with(['guiaRemision', 'asignaciones', 'mercaderiaMalEstado'])->find($id);
    }

    public function updateEstadoRemision(int $id, string $estado): bool
    {
        return (bool) GuiaRemision::where('id', $id)->update(['estado' => $estado]);
    }

    public function updateRemision(int $id, array $data): bool
    {
        return (bool) GuiaRemision::where('id', $id)->update($data);
    }

    public function updateEstadoRuta(int $id, string $estado): bool
    {
        return (bool) GuiaRuta::where('id', $id)->update(['estado' => $estado]);
    }

    public function getPendientesCierre(): Collection
    {
        return GuiaRemision::where('estado', GuiaRemision::ESTADO_CONFIRMACION_CIERRE)
            ->with(['camion', 'operador'])
            ->get();
    }

    public function getRutasByChofer(int $choferId): Collection
    {
        return GuiaRuta::whereHas('guiaRemision.camion', function ($query) use ($choferId) {
            $query->where('chofer_id', $choferId);
        })
        ->where('estado', GuiaRuta::ESTADO_ACTIVA)
        ->with(['asignaciones.pedido'])
        ->get();
    }

    public function getResumenCaja(int $guiaRutaId): array
    {
        // Get all pedidos assigned to this guia_ruta that were delivered (any state)
        $pedidoIds = DB::table('asignacion_pedido_camion')
            ->where('guia_ruta_id', $guiaRutaId)
            ->pluck('pedido_id');

        if ($pedidoIds->isEmpty()) {
            return [
                'totales' => ['total' => 0, 'entregados' => 0, 'parciales' => 0, 'no_entregados' => 0],
                'recaudacion' => ['efectivo' => 0, 'bancos' => 0, 'de_una' => 0, 'total' => 0],
            ];
        }

        $pedidos = DB::table('pedidos')->whereIn('id', $pedidoIds)->get();

        $entregados    = $pedidos->whereIn('estado', ['entregado'])->count();
        $parciales     = $pedidos->where('estado', 'entregado_parcialmente')->count();
        $noEntregados  = $pedidos->where('estado', 'no_entregado')->count();

        // Recaudacion: sum totales by metodo_pago for delivered orders
        $entregadosIds = $pedidos->whereIn('estado', ['entregado', 'entregado_parcialmente'])->pluck('id');

        $recaudacion = DB::table('pedidos')
            ->whereIn('id', $entregadosIds)
            ->selectRaw("
                SUM(CASE WHEN metodo_pago = 'efectivo' THEN COALESCE(valor_entrega, total) ELSE 0 END) as efectivo,
                SUM(CASE WHEN metodo_pago IN ('tc','td','tarjeta') THEN COALESCE(valor_entrega, total) ELSE 0 END) as bancos,
                SUM(CASE WHEN metodo_pago IN ('de_una','deposito') THEN COALESCE(valor_entrega, total) ELSE 0 END) as de_una,
                SUM(COALESCE(valor_entrega, total)) as total_general
            ")
            ->first();

        return [
            'totales' => [
                'total'         => $pedidos->count(),
                'entregados'    => $entregados,
                'parciales'     => $parciales,
                'no_entregados' => $noEntregados,
            ],
            'recaudacion' => [
                'efectivo' => round((float)($recaudacion->efectivo ?? 0), 2),
                'bancos'   => round((float)($recaudacion->bancos   ?? 0), 2),
                'de_una'   => round((float)($recaudacion->de_una   ?? 0), 2),
                'total'    => round((float)($recaudacion->total_general ?? 0), 2),
            ],
        ];
    }

    public function getGuiasResumen(array $filtros): Collection
    {
        $query = GuiaRemision::with(['camion.chofer', 'operador', 'revisor', 'guiasRuta.asignaciones.pedido']);

        if (!empty($filtros['fecha_inicio'])) {
            $query->whereDate('fecha_generacion', '>=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $query->whereDate('fecha_generacion', '<=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        $guias = $query->orderBy('fecha_generacion', 'desc')->get();

        return $guias->map(function ($guia) {
            $pedidoIds = $guia->guiasRuta->flatMap(function ($r) {
                return $r->asignaciones->pluck('pedido_id');
            })->unique();

            $totalPedidos = $pedidoIds->count();

            if ($totalPedidos > 0) {
                $pedidos = DB::table('pedidos')->whereIn('id', $pedidoIds)->get();
                $entregadosIds = $pedidos->whereIn('estado', ['entregado', 'entregado_parcialmente'])->pluck('id');

                $totalEntregado = (float) DB::table('pedidos')
                    ->whereIn('id', $entregadosIds)
                    ->sum(DB::raw('COALESCE(valor_entrega, total)'));

                // Partial returns calculation from items_pedido: (solicitada - entregada) * precio_unitario * 1.15
                $totalDevolucionesParciales = (float) DB::table('items_pedido')
                    ->whereIn('pedido_id', $pedidoIds)
                    ->whereRaw('cantidad_entregada < cantidad_solicitada')
                    ->sum(DB::raw('(cantidad_solicitada - cantidad_entregada) * precio_unitario * 1.15'));

                // Total returns for full cancellations / not delivered if any
                $totalDevolucionesFull = (float) DB::table('pedidos')
                    ->whereIn('id', $pedidoIds)
                    ->whereIn('estado', ['no_entregado', 'cancelado'])
                    ->sum('total');

                $totalDevoluciones = round($totalDevolucionesParciales + $totalDevolucionesFull, 2);
                $totalEntregado = round($totalEntregado, 2);
            } else {
                $totalDevoluciones = 0.0;
                $totalEntregado = 0.0;
            }

            return [
                'id' => $guia->id,
                'fecha_generacion' => $guia->fecha_generacion,
                'estado' => $guia->estado,
                'total_pedidos' => $totalPedidos,
                'total_entregado' => $totalEntregado,
                'total_devoluciones' => $totalDevoluciones,
                'chofer_nombre' => $guia->camion?->chofer?->nombre ?? 'Sin asignar',
                'camion_placa' => $guia->camion?->placa ?? 'N/A',
                'revisada_por' => $guia->revisor?->nombre,
                'fecha_revision' => $guia->fecha_revision,
            ];
        });
    }

    public function getDetalleGuiaCierre(int $guiaId): array
    {
        $guia = GuiaRemision::with(['camion.chofer', 'operador', 'revisor', 'guiasRuta.asignaciones.pedido.cliente.usuario', 'guiasRuta.asignaciones.pedido.direccion', 'guiasRuta.asignaciones.pedido.items.producto'])->findOrFail($guiaId);

        $pedidoIds = $guia->guiasRuta->flatMap(function ($r) {
            return $r->asignaciones->pluck('pedido_id');
        })->unique();

        $pedidos = DB::table('pedidos')->whereIn('id', $pedidoIds)->get();

        $entregadosIds = $pedidos->whereIn('estado', ['entregado', 'entregado_parcialmente'])->pluck('id');

        $recaudacion = DB::table('pedidos')
            ->whereIn('id', $entregadosIds)
            ->selectRaw("
                SUM(CASE WHEN metodo_pago = 'efectivo' THEN COALESCE(valor_entrega, total) ELSE 0 END) as efectivo,
                SUM(CASE WHEN metodo_pago IN ('tc','td','tarjeta') THEN COALESCE(valor_entrega, total) ELSE 0 END) as bancos,
                SUM(CASE WHEN metodo_pago IN ('de_una','deposito') THEN COALESCE(valor_entrega, total) ELSE 0 END) as de_una,
                SUM(COALESCE(valor_entrega, total)) as total_general
            ")
            ->first();

        // Summary of returned products
        $devueltos = DB::table('items_pedido')
            ->join('productos', 'items_pedido.producto_id', '=', 'productos.id')
            ->whereIn('items_pedido.pedido_id', $pedidoIds)
            ->whereRaw('items_pedido.cantidad_entregada < items_pedido.cantidad_solicitada')
            ->selectRaw("
                productos.nombre as producto,
                SUM(items_pedido.cantidad_solicitada - items_pedido.cantidad_entregada) as cantidad_devuelta,
                SUM((items_pedido.cantidad_solicitada - items_pedido.cantidad_entregada) * items_pedido.precio_unitario * 1.15) as total_usd,
                COALESCE(items_pedido.motivo_devolucion, 'Otro motivo') as motivo
            ")
            ->groupBy('productos.nombre', 'items_pedido.motivo_devolucion')
            ->get();

        // Orders list mapped
        $pedidosLista = $guia->guiasRuta->flatMap(function ($r) {
            return $r->asignaciones->map(function ($asig) {
                $p = $asig->pedido;
                if (!$p) return null;
                return [
                    'id' => $p->id,
                    'idPedido' => $p->idPedido ?? ('PED-' . str_pad((string)$p->id, 5, '0', STR_PAD_LEFT)),
                    'cliente' => $p->cliente?->razon_social ?? $p->cliente?->usuario?->nombre ?? 'Cliente',
                    'direccion' => $p->direccion?->descripcion ?? 'N/A',
                    'metodo_pago' => strtoupper($p->metodo_pago ?? 'EFECTIVO'),
                    'total' => round((float)$p->total, 2),
                    'estado' => $p->estado,
                ];
            })->filter();
        })->values();

        return [
            'guia' => [
                'id' => $guia->id,
                'fecha_generacion' => $guia->fecha_generacion,
                'estado' => $guia->estado,
                'efectivo_declarado' => (float)$guia->efectivo_declarado,
                'chofer_nombre' => $guia->camion?->chofer?->nombre ?? 'Sin asignar',
                'camion_placa' => $guia->camion?->placa ?? 'N/A',
                'revisada_por' => $guia->revisor?->nombre,
                'fecha_revision' => $guia->fecha_revision,
            ],
            'resumen_caja' => [
                'efectivo' => round((float)($recaudacion->efectivo ?? 0), 2),
                'bancos' => round((float)($recaudacion->bancos ?? 0), 2),
                'de_una' => round((float)($recaudacion->de_una ?? 0), 2),
                'total' => round((float)($recaudacion->total_general ?? 0), 2),
            ],
            'productos_devueltos' => $devueltos->map(function ($d) {
                return [
                    'producto' => $d->producto,
                    'cantidad_devuelta' => (int)$d->cantidad_devuelta,
                    'total_usd' => round((float)$d->total_usd, 2),
                    'motivo' => $d->motivo,
                ];
            }),
            'pedidos' => $pedidosLista,
        ];
    }

    public function aprobarRevisionGuia(int $guiaId, int $userId): bool
    {
        return (bool) GuiaRemision::where('id', $guiaId)->update([
            'estado' => GuiaRemision::ESTADO_REVISADA,
            'revisada_por' => $userId,
            'fecha_revision' => now(),
        ]);
    }
}

