<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CarritoAbandonadoRepositoryInterface;
use App\Models\CarritoAbandonado;

class CarritoAbandonadoService
{
    public function __construct(private readonly CarritoAbandonadoRepositoryInterface $repository)
    {
    }

    public function registrar(array $data): CarritoAbandonado
    {
        return $this->repository->create($data);
    }
}
