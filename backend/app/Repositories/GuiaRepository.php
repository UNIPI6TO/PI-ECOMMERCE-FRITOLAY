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
                SUM(CASE WHEN metodo_pago = 'efectivo' THEN total ELSE 0 END) as efectivo,
                SUM(CASE WHEN metodo_pago IN ('tc','td','tarjeta') THEN total ELSE 0 END) as bancos,
                SUM(CASE WHEN metodo_pago IN ('de_una','deposito') THEN total ELSE 0 END) as de_una,
                SUM(total) as total_general
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
}
