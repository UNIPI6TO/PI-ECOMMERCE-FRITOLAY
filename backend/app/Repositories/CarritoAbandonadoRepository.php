<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\CarritoAbandonadoRepositoryInterface;
use App\Models\CarritoAbandonado;
use Illuminate\Database\Eloquent\Collection;

class CarritoAbandonadoRepository implements CarritoAbandonadoRepositoryInterface
{
    public function create(array $data): CarritoAbandonado
    {
        return CarritoAbandonado::create($data);
    }

    public function getAll(array $filters): Collection
    {
        $query = CarritoAbandonado::query();
        
        return $query->get();
    }
}
