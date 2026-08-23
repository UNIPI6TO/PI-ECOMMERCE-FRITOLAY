<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GpsRequest;
use App\Services\FirestoreGpsService;

class GpsController extends Controller
{
    public function __construct(
        private readonly FirestoreGpsService $gpsService
    ) {}

    public function actualizarUbicacion(GpsRequest $request)
    {
        $this->gpsService->escribirUbicacion(
            (int)$request->input('camion_id'),
            (int)request('user_id'),
            (int)$request->input('guia_ruta_id'),
            (float)$request->input('latitud'),
            (float)$request->input('longitud'),
            $request->input('estado')
        );

        return response()->json(['message' => 'Ubicación actualizada']);
    }
}
