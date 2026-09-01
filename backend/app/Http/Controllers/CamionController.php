<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CamionRequest;
use App\Http\Requests\CamionEstadoRequest;
use App\Http\Requests\CamionChoferRequest;
use App\Services\CamionService;
use Exception;
use Illuminate\Http\Request;

class CamionController extends Controller
{
    public function __construct(
        private readonly CamionService $camionService
    ) {}

        public function myCamion(\Illuminate\Http\Request $request)
    {
        $camion = \App\Models\Camion::where('chofer_id', $request->user_id)->first();
        if (!$camion) {
            return response()->json(null, 404);
        }
        return response()->json($camion);
    }

public function index(Request $request)
    {
        return response()->json($this->camionService->getAll($request->all()));
    }

    public function store(CamionRequest $request)
    {
        try {
            $camion = $this->camionService->crearCamion($request->validated(), (int)request('user_id'));
            return response()->json(['message' => 'Camión creado', 'data' => $camion], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    
    public function update(int $id, CamionRequest $request)
    {
        try {
            $camion = $this->camionService->actualizarCamion($id, $request->validated(), (int)request('user_id'));
            return response()->json(['message' => 'Camión actualizado', 'data' => $camion]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function updateEstado(int $id, CamionEstadoRequest $request)
    {
        try {
            $camion = $this->camionService->cambiarEstado($id, $request->input('estado'), (int)request('user_id'));
            return response()->json(['message' => 'Estado actualizado', 'data' => $camion]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function asignarChofer(int $id, CamionChoferRequest $request)
    {
        try {
            $camion = $this->camionService->asignarChofer($id, (int)$request->input('chofer_id'), (int)request('user_id'));
            return response()->json(['message' => 'Chofer asignado', 'data' => $camion]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
