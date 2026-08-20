<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioAdminRequest;
use App\Services\UsuarioAdminService;
use Illuminate\Http\Request;

class UsuarioAdminController extends Controller
{
    public function __construct(
        private readonly UsuarioAdminService $usuarioAdminService
    ) {}

    public function index()
    {
        return response()->json($this->usuarioAdminService->listarEmpleados());
    }

    public function store(UsuarioAdminRequest $request)
    {
        $empleado = $this->usuarioAdminService->crearEmpleado($request->validated(), auth()->id());
        return response()->json(['message' => 'Empleado creado', 'data' => $empleado], 201);
    }

    public function inactivar(int $id)
    {
        $this->usuarioAdminService->inactivar($id, auth()->id());
        return response()->json(['message' => 'Empleado inactivado']);
    }

    public function activar(int $id)
    {
        $this->usuarioAdminService->activar($id, auth()->id());
        return response()->json(['message' => 'Empleado activado']);
    }

    public function resetearPassword(int $id)
    {
        $newPassword = $this->usuarioAdminService->resetearPassword($id, auth()->id());
        return response()->json(['message' => 'Password reseteado', 'data' => ['password' => $newPassword]]);
    }
}
