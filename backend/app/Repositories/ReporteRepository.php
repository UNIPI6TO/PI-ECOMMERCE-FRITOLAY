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
        return DB::table('pedidos')
            ->select(DB::raw('DATE(creado_en) as fecha'), DB::raw('SUM(total) as total'))
            ->whereIn('estado', ['entregado', 'entregado_parcialmente'])
            ->whereBetween('creado_en', [$inicio, $fin])
            ->groupBy(DB::raw('DATE(creado_en)'))
            ->get();
    }

    public function getVentasPorCamion(Carbon $inicio, Carbon $fin): Collection
    {
        return DB::table('pedidos')
            ->join('asignacion_pedido_camion', 'pedidos.id', '=', 'asignacion_pedido_camion.pedido_id')
            ->join('guias_ruta', 'asignacion_pedido_camion.guia_ruta_id', '=', 'guias_ruta.id')
            ->join('guias_remision', 'guias_ruta.guia_remision_id', '=', 'guias_remision.id')
            ->select('guias_remision.camion_id', DB::raw('SUM(pedidos.total) as total'))
            ->whereIn('pedidos.estado', ['entregado', 'entregado_parcialmente'])
            ->whereBetween('pedidos.creado_en', [$inicio, $fin])
            ->groupBy('guias_remision.camion_id')
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
        return DB::table('productos')->get();
    }

    public function getStockPorCamion(): Collection
    {
        return DB::table('bodega_camion')
            ->join('camiones', 'bodega_camion.camion_id', '=', 'camiones.id')
            ->join('productos', 'bodega_camion.producto_id', '=', 'productos.id')
            ->select('camiones.placa', 'productos.nombre', 'bodega_camion.cantidad_fisica')
            ->get();
    }
}
