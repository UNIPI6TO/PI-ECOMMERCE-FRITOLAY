<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Descuento;
use Illuminate\Database\Eloquent\Collection;

class DescuentoService
{
    public function calcularDescuento(int $clienteId, string $metodoPago, float $subtotal): float
    {
        $now = now();
        
        $descuentoIndividual = Descuento::where('cliente_id', $clienteId)
            ->where('tipo', 'individual')
            ->whereIn('metodo_pago', [$metodoPago, 'todos'])
            ->where('fecha_caducidad', '>', $now)
            ->orderByDesc('porcentaje')
            ->first();

        $descuentoGlobal = Descuento::where('tipo', 'global')
            ->whereIn('metodo_pago', [$metodoPago, 'todos'])
            ->where('fecha_caducidad', '>', $now)
            ->orderByDesc('porcentaje')
            ->first();

        $pctIndividual = $descuentoIndividual ? $descuentoIndividual->porcentaje : 0;
        $pctGlobal = $descuentoGlobal ? $descuentoGlobal->porcentaje : 0;

        $maxPct = max($pctIndividual, $pctGlobal);

        return $subtotal * ($maxPct / 100);
    }

    public function getDescuentosVigentes(): Collection
    {
        return Descuento::where('fecha_caducidad', '>', now())->get();
    }

    public function crearDescuento(array $data): Descuento
    {
        return Descuento::create($data);
    }

    public function eliminarDescuento(int $id): bool
    {
        $descuento = Descuento::find($id);
        if ($descuento) {
            return $descuento->delete();
        }
        return false;
    }
}
