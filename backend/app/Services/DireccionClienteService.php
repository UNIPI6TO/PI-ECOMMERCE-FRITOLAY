<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DireccionCliente;

class DireccionClienteService
{
    public function setDefault(int $clienteId, int $direccionId): void
    {
        DireccionCliente::where('cliente_id', $clienteId)->update(['es_por_defecto' => false]);
        DireccionCliente::where('id', $direccionId)->where('cliente_id', $clienteId)->update(['es_por_defecto' => true]);
    }

    public function create(int $clienteId, array $data): DireccionCliente
    {
        $data['cliente_id'] = $clienteId;
        if (!empty($data['es_por_defecto'])) {
            DireccionCliente::where('cliente_id', $clienteId)->update(['es_por_defecto' => false]);
        }
        return DireccionCliente::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $direccion = DireccionCliente::find($id);
        if (!$direccion) return false;

        if (!empty($data['es_por_defecto'])) {
            DireccionCliente::where('cliente_id', $direccion->cliente_id)->update(['es_por_defecto' => false]);
        }
        return $direccion->update($data);
    }

    public function delete(int $id): bool
    {
        $direccion = DireccionCliente::find($id);
        if ($direccion) {
            $direccion->estado = false;
            return $direccion->save();
        }
        return false;
    }
}
