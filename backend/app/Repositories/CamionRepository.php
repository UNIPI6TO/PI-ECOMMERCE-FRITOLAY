<?php declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\CamionRepositoryInterface;
use App\Models\Camion;
use Illuminate\Support\Collection;

class CamionRepository implements CamionRepositoryInterface
{
    public function getActivos(): Collection
    {
        return Camion::where('estado', Camion::ESTADO_ACTIVO)->get();
    }

    public function findById(int $id): ?Camion
    {
        return Camion::with(['chofer', 'bodega.producto'])->find($id);
    }

    public function create(array $data): Camion
    {
        return Camion::create($data);
    }

    public function update(int $id, array $data): Camion
    {
        $camion = Camion::findOrFail($id);
        $camion->update($data);
        return $camion->fresh(['chofer', 'bodega.producto']);
    }

    public function updateEstado(int $id, string $estado): bool
    {
        return (bool) Camion::where('id', $id)->update(['estado' => $estado]);
    }

    public function asignarChofer(int $camionId, int $choferId): bool
    {
        return (bool) Camion::where('id', $camionId)->update(['chofer_id' => $choferId]);
    }

    public function getAll(): Collection
    {
        return Camion::with(['chofer'])->get();
    }
}
