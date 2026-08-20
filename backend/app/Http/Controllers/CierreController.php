<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ArqueoRequest;
use App\Http\Requests\IngresoInventarioRequest;
use App\Http\Requests\MalEstadoRequest;
use App\Http\Requests\CierreRequest;
use App\Services\CierreService;
use Exception;
use Illuminate\Http\Request;

class CierreController extends Controller
{
    public function __construct(
        private readonly CierreService $cierreService
    ) {}

    public function resumenCaja(int $id)
    {
        return response()->json($this->cierreService->getResumenCaja($id));
    }

    public function arqueo(int $id, ArqueoRequest $request)
    {
        try {
            $guia = $this->cierreService->declararArqueo($id, (float) $request->input('efectivo_declarado'), auth()->id());
            return response()->json(['message' => 'Arqueo declarado exitosamente', 'data' => $guia]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function detalle(int $id)
    {
        // ... omitted for brevity ...
        return response()->json(['message' => 'Detalle de guia']);
    }

    public function ingresoInventario(IngresoInventarioRequest $request)
    {
        // ...
        return response()->json(['message' => 'Ingreso registrado']);
    }

    public function malEstado(MalEstadoRequest $request)
    {
        $mercaderias = [
            [
                'producto_id' => $request->input('producto_id'),
                'cantidad' => $request->input('cantidad'),
                'motivo' => $request->input('motivo'),
                'estado' => 'mal_estado'
            ]
        ];
        $this->cierreService->procesarMercaderiaDevuelta($request->input('guia_ruta_id'), $mercaderias, auth()->id());
        return response()->json(['message' => 'Mercadería en mal estado registrada']);
    }

    public function cerrar(int $id, CierreRequest $request)
    {
        try {
            $guia = $this->cierreService->cerrarGuia($id, (float) $request->input('efectivo_recibido'), auth()->id());
            return response()->json(['message' => 'Guía cerrada', 'data' => $guia]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function pendientesCierre()
    {
        return response()->json($this->cierreService->getPendientesCierre());
    }
}
