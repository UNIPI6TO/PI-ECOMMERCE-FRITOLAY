<?php declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\BodegaRepositoryInterface;
use App\Models\BodegaCamion;
use App\Models\TransaccionInventario;
use Illuminate\Support\Collection;

class BodegaRepository implements BodegaRepositoryInterface
{
    public function upsert(int $camionId, int $productoId, float $cantidad): void
    {
        $bodega = BodegaCamion::where('camion_id', $camionId)
            ->where('producto_id', $productoId)
            ->first();
            
        if ($bodega) {
            $bodega->update(['cantidad_actual' => $cantidad]);
        } else {
            BodegaCamion::create([
                'camion_id' => $camionId,
                'producto_id' => $productoId,
                'cantidad_actual' => $cantidad
            ]);
        }
    }

    public function decrementar(int $camionId, int $productoId, float $cantidad): void
    {
        BodegaCamion::where('camion_id', $camionId)
            ->where('producto_id', $productoId)
            ->decrement('cantidad_actual', $cantidad);
    }

    public function incrementar(int $camionId, int $productoId, float $cantidad): void
    {
        $this->upsert($camionId, $productoId, $cantidad); // Assuming increment logic requires handling non-existent records
        // Wait, proper increment:
        $bodega = BodegaCamion::where('camion_id', $camionId)
            ->where('producto_id', $productoId)
            ->first();
        if ($bodega) {
            $bodega->increment('cantidad_actual', $cantidad);
        } else {
            $this->upsert($camionId, $productoId, $cantidad);
        }
    }

    public function encerar(int $camionId): void
    {
        BodegaCamion::where('camion_id', $camionId)->update(['cantidad_actual' => 0.0]);
    }

    public function getByCamion(int $camionId): Collection
    {
        return BodegaCamion::where('camion_id', $camionId)
            ->with(['producto'])
            ->get();
    }

    public function createTransaccion(array $data): TransaccionInventario
    {
        return TransaccionInventario::create($data);
    }
}
