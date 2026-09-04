<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteRepository
{
    public function getVentasPorDia(Carbon $inicio, Carbon $fin): Collection
    {
        $diasDiff = $inicio->diffInDays($fin);
        $groupByRaw = $diasDiff <= 2 
            ? "DATE_FORMAT(p.creado_en, '%Y-%m-%d %H:00')" 
            : "DATE(p.creado_en)";

        return DB::table('pedidos as p')
            ->select(
                DB::raw("{$groupByRaw} as fecha"),
                DB::raw('SUM(CASE 
                    WHEN p.estado = "entregado" THEN p.total
                    WHEN p.estado = "entregado_parcialmente" THEN (
                        SELECT COALESCE(SUM(ip.cantidad_entregada * ip.precio_unitario * 1.15), 0)
                        FROM items_pedido ip WHERE ip.pedido_id = p.id
                    )
                    ELSE 0 
                END) as total')
            )
            ->whereIn('p.estado', ['entregado', 'entregado_parcialmente'])
            ->whereBetween('p.creado_en', [$inicio, $fin])
            ->groupBy(DB::raw($groupByRaw))
            ->get();
    }

    public function getVentasPorCamion(Carbon $inicio, Carbon $fin): Collection
    {
        return DB::table('pedidos as p')
            ->join('asignacion_pedido_camion', 'p.id', '=', 'asignacion_pedido_camion.pedido_id')
            ->join('guias_ruta', 'asignacion_pedido_camion.guia_ruta_id', '=', 'guias_ruta.id')
            ->join('guias_remision', 'guias_ruta.guia_remision_id', '=', 'guias_remision.id')
            ->leftJoin('camiones', 'guias_remision.camion_id', '=', 'camiones.id')
            ->select(
                'guias_remision.camion_id',
                'camiones.placa',
                DB::raw('SUM(CASE 
                    WHEN p.estado = "entregado" THEN p.total
                    WHEN p.estado = "entregado_parcialmente" THEN (
                        SELECT COALESCE(SUM(ip.cantidad_entregada * ip.precio_unitario * 1.15), 0)
                        FROM items_pedido ip WHERE ip.pedido_id = p.id
                    )
                    ELSE 0 
                END) as total')
            )
            ->whereIn('p.estado', ['entregado', 'entregado_parcialmente'])
            ->whereBetween('p.creado_en', [$inicio, $fin])
            ->groupBy('guias_remision.camion_id', 'camiones.placa')
            ->get();
    }

    public function getRecaudacionPorMetodo(Carbon $inicio, Carbon $fin): Collection
    {
        return DB::table('pedidos')
            ->select('metodo_pago', DB::raw('SUM(total) as total'))
            ->whereIn('estado', ['entregado', 'entregado_parcialmente'])
            ->whereBetween('creado_en', [$inicio, $fin])
            ->groupBy('metodo_pago')
            ->get();
    }

    public function getPedidosCountPorEstado(Carbon $inicio, Carbon $fin): array
    {
        return DB::table('pedidos')
            ->select('estado', DB::raw('COUNT(*) as cantidad'))
            ->whereBetween('creado_en', [$inicio, $fin])
            ->groupBy('estado')
            ->pluck('cantidad', 'estado')
            ->toArray();
    }

    public function getStockMaestro(): Collection
    {
        return DB::table('productos')
            ->select('id', 'nombre', 'cantidad_fisica', 'en_pedidos',
                DB::raw('(cantidad_fisica - en_pedidos) as disponible'))
            ->orderBy('disponible', 'asc')
            ->get();
    }

    public function getStockPorCamion(): Collection
    {
        return DB::table('bodega_camion')
            ->join('camiones', 'bodega_camion.camion_id', '=', 'camiones.id')
            ->join('productos', 'bodega_camion.producto_id', '=', 'productos.id')
            ->select('camiones.placa', 'productos.nombre', 'bodega_camion.cantidad_actual')
            ->where('bodega_camion.cantidad_actual', '>', 0)
            ->orderBy('camiones.placa')
            ->orderBy('productos.nombre')
            ->get();
    }

    public function getTopPerdidasPorMotivo(Carbon $inicio, Carbon $fin): Collection
    {
        $carritos = DB::table('carritos_abandonados')
            ->select(
                DB::raw('COALESCE(motivo_cancelacion, "Sin motivo especificado") as motivo'),
                DB::raw('"Abandono Carrito" as origen'),
                DB::raw('COUNT(*) as conteo'),
                DB::raw('SUM(valor_total) as total_perdido')
            )
            ->whereBetween('fecha_abandono', [$inicio, $fin])
            ->groupBy('motivo');

        $pedidosPerdidos = DB::table('pedidos')
            ->select(
                DB::raw('COALESCE(motivo_cancelacion, "Sin motivo especificado") as motivo'),
                DB::raw('CASE WHEN estado = "cancelado" THEN "Pedido Cancelado" ELSE "Pedido Devuelto / No Entregado" END as origen'),
                DB::raw('COUNT(*) as conteo'),
                DB::raw('SUM(total) as total_perdido')
            )
            ->whereIn('estado', ['cancelado', 'no_entregado'])
            ->whereBetween('creado_en', [$inicio, $fin])
            ->groupBy('motivo', 'estado');

        $unificado = $carritos->unionAll($pedidosPerdidos)->get();

        $agrupado = $unificado->groupBy('motivo')->map(function ($items, $motivo) {
            return [
                'motivo' => $motivo,
                'conteo' => (int) $items->sum('conteo'),
                'total_perdido' => round((float) $items->sum('total_perdido'), 2),
                'origen' => $items->pluck('origen')->unique()->implode(', ')
            ];
        })->sortByDesc('total_perdido')->values()->take(10);

        return $agrupado;
    }

    public function getPerdidasPorDia(Carbon $inicio, Carbon $fin): Collection
    {
        $diasDiff = $inicio->diffInDays($fin);
        $groupByCarritos = $diasDiff <= 2 
            ? "DATE_FORMAT(fecha_abandono, '%Y-%m-%d %H:00')" 
            : "DATE(fecha_abandono)";
        $groupByPedidos = $diasDiff <= 2 
            ? "DATE_FORMAT(creado_en, '%Y-%m-%d %H:00')" 
            : "DATE(creado_en)";

        $carritos = DB::table('carritos_abandonados')
            ->select(
                DB::raw("{$groupByCarritos} as fecha"),
                DB::raw('SUM(valor_total) as total_perdido')
            )
            ->whereBetween('fecha_abandono', [$inicio, $fin])
            ->groupBy(DB::raw($groupByCarritos));

        $pedidos = DB::table('pedidos')
            ->select(
                DB::raw("{$groupByPedidos} as fecha"),
                DB::raw('SUM(total) as total_perdido')
            )
            ->whereIn('estado', ['cancelado', 'no_entregado'])
            ->whereBetween('creado_en', [$inicio, $fin])
            ->groupBy(DB::raw($groupByPedidos));

        $unificado = $carritos->unionAll($pedidos)->get();

        return $unificado->groupBy('fecha')->map(function ($items, $fecha) {
            return [
                'fecha' => $fecha,
                'total_perdido' => round((float) $items->sum('total_perdido'), 2)
            ];
        })->sortBy('fecha')->values();
    }

    public function getTendenciaVolumenYVentas(Carbon $inicio, Carbon $fin): Collection
    {
        $diasDiff = $inicio->diffInDays($fin);
        $groupByRaw = $diasDiff <= 2 
            ? "DATE_FORMAT(p.creado_en, '%Y-%m-%d %H:00')" 
            : "DATE(p.creado_en)";

        return DB::table('pedidos as p')
            ->select(
                DB::raw("{$groupByRaw} as fecha"),
                DB::raw('COUNT(*) as cantidad_pedidos'),
                DB::raw('SUM(CASE 
                    WHEN p.estado = "entregado" THEN p.total
                    WHEN p.estado = "entregado_parcialmente" THEN (
                        SELECT COALESCE(SUM(ip.cantidad_entregada * ip.precio_unitario * 1.15), 0)
                        FROM items_pedido ip WHERE ip.pedido_id = p.id
                    )
                    ELSE 0 
                END) as ventas_entregadas'),
                DB::raw('SUM(p.total) as ventas_totales')
            )
            ->whereBetween('p.creado_en', [$inicio, $fin])
            ->where('p.estado', '!=', 'cancelado')
            ->groupBy(DB::raw($groupByRaw))
            ->orderBy('fecha', 'asc')
            ->get();
    }

    public function getKpisGenerales(Carbon $inicio, Carbon $fin): array
    {
        // 1. # Pedidos y $ Total Pedidos (excluyendo cancelados por clientes)
        $stats = DB::table('pedidos')
            ->select(
                DB::raw('COUNT(*) as cantidad_total'),
                DB::raw('SUM(total) as valor_total'),
                DB::raw('SUM(CASE WHEN estado IN ("entregado", "entregado_parcialmente") THEN 1 ELSE 0 END) as pedidos_entregados')
            )
            ->whereBetween('creado_en', [$inicio, $fin])
            ->where('estado', '!=', 'cancelado')
            ->first();

        $cantidadTotal = (int) ($stats->cantidad_total ?? 0);
        $valorTotal = (float) ($stats->valor_total ?? 0);
        $pedidosEntregados = (int) ($stats->pedidos_entregados ?? 0);

        // 2. $ Entregado (Exclusivamente lo entregado de forma efectiva)
        $totalEntregado = (float) DB::table('pedidos as p')
            ->whereBetween('p.creado_en', [$inicio, $fin])
            ->where('p.estado', '!=', 'cancelado')
            ->sum(DB::raw('CASE 
                WHEN p.estado = "entregado" THEN p.total
                WHEN p.estado = "entregado_parcialmente" THEN (
                    SELECT COALESCE(SUM(ip.cantidad_entregada * ip.precio_unitario * 1.15), 0)
                    FROM items_pedido ip WHERE ip.pedido_id = p.id
                )
                ELSE 0 
            END'));

        // 3. $ Devoluciones (Filtrado ultra-rápido por fecha_pedido indexado)
        $totalDevoluciones = (float) DB::table('notas_credito as nc')
            ->join('facturas as f', 'f.id', '=', 'nc.factura_id')
            ->join('pedidos as p', 'p.id', '=', 'f.pedido_id')
            ->whereBetween('nc.fecha_pedido', [$inicio, $fin])
            ->where('p.estado', '!=', 'cancelado')
            ->sum('nc.valor_total');

        // 4. Efectividad Entrega
        $efectividad = $cantidadTotal > 0 ? round(($pedidosEntregados / $cantidadTotal) * 100, 2) : 0;

        // 5. Recaudación Efectivo
        $recaudacionEfectivo = (float) DB::table('pedidos')
            ->whereBetween('creado_en', [$inicio, $fin])
            ->where('estado', '!=', 'cancelado')
            ->where('metodo_pago', 'efectivo')
            ->whereIn('estado', ['entregado', 'entregado_parcialmente'])
            ->sum('total');

        return [
            'cantidad_total_pedidos' => $cantidadTotal,
            'valor_total_pedidos' => round($valorTotal, 2),
            'ventas_entregadas_total' => round($totalEntregado, 2),
            'total_devoluciones' => round($totalDevoluciones, 2),
            'pedidos_entregados_count' => $pedidosEntregados,
            'efectividad_porcentaje' => $efectividad,
            'recaudacion_efectivo' => round($recaudacionEfectivo, 2)
        ];
    }

    public function getCarritosAbandonados(Carbon $inicio, Carbon $fin): Collection
    {
        return DB::table('carritos_abandonados')
            ->leftJoin('clientes', 'carritos_abandonados.cliente_id', '=', 'clientes.id')
            ->leftJoin('usuarios', 'clientes.usuario_id', '=', 'usuarios.id')
            ->select(
                'carritos_abandonados.id',
                DB::raw('COALESCE(clientes.razon_social, usuarios.nombre, "Cliente Anónimo / Invitado") as cliente'),
                'carritos_abandonados.motivo_cancelacion as motivo',
                'carritos_abandonados.valor_total as monto',
                'carritos_abandonados.fecha_abandono as fecha'
            )
            ->whereBetween('carritos_abandonados.fecha_abandono', [$inicio, $fin])
            ->orderBy('carritos_abandonados.fecha_abandono', 'desc')
            ->get();
    }
}
