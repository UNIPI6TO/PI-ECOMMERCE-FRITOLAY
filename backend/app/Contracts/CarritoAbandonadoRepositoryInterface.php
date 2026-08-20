<?php

declare(strict_types=1);

namespace App\Contracts;

interface CarritoAbandonadoRepositoryInterface
{
    public function create(array $data);
    public function getAll(array $filters);
}
