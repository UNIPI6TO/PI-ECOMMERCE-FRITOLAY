<?php declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\GuiaRepositoryInterface;
use App\Models\GuiaRemision;
use App\Models\GuiaRuta;
use App\Models\AsignacionPedidoCamion;
use Illuminate\Support\Collection;

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
}
