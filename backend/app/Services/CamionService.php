<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CamionRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Collection;

class CamionService
{
    public function __construct(
        private readonly CamionRepositoryInterface $camionRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly AuditoriaService $auditoriaService
    ) {}

    public function crearCamion(array $data, int $adminId): object
    {
        $camion = $this->camionRepository->create(array_merge($data, ['estado' => 'activo']));
        $this->auditoriaService->log('creacion_camion', 'Se creó el camión ' . $camion->placa, $adminId);
        return $camion;
    }

    public function cambiarEstado(int $camionId, string $nuevoEstado, int $adminId): object
    {
        $camion = $this->camionRepository->update($camionId, ['estado' => $nuevoEstado]);
        $this->auditoriaService->log('cambio_estado_camion', 'Camión ' . $camionId . ' cambió a ' . $nuevoEstado, $adminId);
        return $camion;
    }

    public function asignarChofer(int $camionId, int $choferId, int $adminId): object
    {
        $chofer = $this->userRepository->findById($choferId);
        if (!$chofer || $chofer->rol !== 'chofer') {
            throw new Exception('El usuario no es un chofer válido.');
        }
        
        $camion = $this->camionRepository->update($camionId, ['chofer_id' => $choferId]);
        $this->auditoriaService->log('asignacion_chofer', 'Se asignó el chofer ' . $choferId . ' al camión ' . $camionId, $adminId);
        return $camion;
    }

    public function getAll(array $filtros = []): Collection
    {
        return $this->camionRepository->getAll($filtros);
    }
}
