<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class UsuarioAdminService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly AuditoriaService $auditoriaService
    ) {}

    public function crearEmpleado(array $data, int $adminId): object
    {
        // En lugar de bcrypt aquí, pasamos el password limpio para que el repositorio lo hashee
        $data['password'] = $data['email']; 
        $empleado = $this->userRepository->create($data);
        $this->auditoriaService->logSimple('creacion_empleado', 'Se creó el empleado ' . $empleado->id, $adminId);
        return $empleado;
    }

    public function actualizarEmpleado(int $id, array $data, int $adminId): object
    {
        $this->userRepository->update($id, $data);
        $this->auditoriaService->logSimple('actualizacion_empleado', 'Se actualizó el empleado ' . $id, $adminId);
        return $this->userRepository->findById($id);
    }

    public function inactivar(int $id, int $adminId): bool
    {
        $res = $this->userRepository->update($id, ['activo' => false]);
        $this->auditoriaService->logSimple('inactivar_empleado', 'Se inactivó el empleado ' . $id, $adminId);
        return (bool)$res;
    }

    public function activar(int $id, int $adminId): bool
    {
        $res = $this->userRepository->update($id, ['activo' => true]);
        $this->auditoriaService->logSimple('activar_empleado', 'Se activó el empleado ' . $id, $adminId);
        return (bool)$res;
    }

    public function resetearPassword(int $id, int $adminId): string
    {
        $newPassword = Str::random(8);
        $this->userRepository->update($id, ['password' => bcrypt($newPassword)]);
        $this->auditoriaService->logSimple('reset_password', 'Se reseteó el password del empleado ' . $id, $adminId);
        return $newPassword;
    }

    public function listarEmpleados(): Collection
    {
        return $this->userRepository->getAll(['roles' => ['administrador', 'operador', 'chofer']]);
    }
}
