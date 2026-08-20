<?php declare(strict_types=1);

namespace App\Contracts;
use App\Models\GuiaRemision;
use App\Models\GuiaRuta;
use App\Models\AsignacionPedidoCamion;
use Illuminate\Support\Collection;

interface GuiaRepositoryInterface
{
    public function createRemision(array $data): GuiaRemision;
    public function createRuta(array $data): GuiaRuta;
    public function createAsignacion(array $data): AsignacionPedidoCamion;
    public function findRemisionById(int $id): ?GuiaRemision;
    public function findRutaById(int $id): ?GuiaRuta;
    public function updateEstadoRemision(int $id, string $estado): bool;
    public function updateEstadoRuta(int $id, string $estado): bool;
    public function getPendientesCierre(): Collection;
    public function getRutasByChofer(int $choferId): Collection;
}
