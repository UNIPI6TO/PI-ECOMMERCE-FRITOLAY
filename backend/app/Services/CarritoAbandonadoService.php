<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CarritoAbandonadoRepositoryInterface;
use App\Models\CarritoAbandonado;
use App\Models\Cliente;

class CarritoAbandonadoService
{
    public function __construct(private readonly CarritoAbandonadoRepositoryInterface $repository)
    {
    }

    public function registrar(array $data): CarritoAbandonado
    {
        if (!empty($data['cliente_id'])) {
            $cliente = Cliente::where('id', $data['cliente_id'])
                ->orWhere('usuario_id', $data['cliente_id'])
                ->first();
            $data['cliente_id'] = $cliente ? $cliente->id : null;
        } else {
            $data['cliente_id'] = null;
        }

        $data['fecha_abandono'] = now();
        return $this->repository->create($data);
    }
}
