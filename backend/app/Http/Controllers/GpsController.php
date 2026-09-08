<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GpsRequest;
use App\Models\Camion;
use App\Services\FirestoreGpsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * GpsController — Receptor de coordenadas GPS del chofer (nativo + browser).
 *
 * Endpoint: POST /api/gps/ubicacion
 * Middleware: jwt + role:chofer
 *
 * Origen de las peticiones:
 *  - gps-native-tracker.js (Capacitor BackgroundGeolocation) → segundo plano nativo
 *  - gps-tracker.js (browser fallback) → primer plano web
 *
 * Flujo:
 *  1. Valida el payload via GpsRequest (camion_id, latitud, longitud, estado)
 *  2. Verifica que el chofer autenticado sea el asignado al camión
 *  3. Delega la escritura en Firestore a FirestoreGpsService
 *  4. En caso de error, registra en log y retorna 200 igualmente
 *     (el cliente tiene cola offline — no queremos que el frontend se bloquee)
 */
class GpsController extends Controller
{
    public function __construct(
        private readonly FirestoreGpsService $gpsService
    ) {}

    public function actualizarUbicacion(GpsRequest $request): JsonResponse
    {
        $camionId   = (int) $request->input('camion_id');
        $guiaRutaId = (int) ($request->input('guia_ruta_id') ?? 0);
        $lat        = (float) $request->input('latitud');
        $lng        = (float) $request->input('longitud');
        $estado     = (string) ($request->input('estado') ?? 'en_movimiento');
        $choferId   = (int) $request->user_id;

        // ── Verificación de autorización ──────────────────────────────────────
        // El chofer solo puede actualizar la ubicación de SU camión asignado.
        $camion = Camion::find($camionId);
        if (!$camion) {
            return response()->json(['error' => 'Camión no encontrado'], 404);
        }

        if ($camion->chofer_id !== $choferId) {
            Log::warning("[GPS] Chofer #{$choferId} intentó actualizar camión #{$camionId} que no le pertenece.");
            return response()->json(['error' => 'No autorizado para este camión'], 403);
        }

        // ── Escritura en Firestore via Service ────────────────────────────────
        try {
            $this->gpsService->escribirUbicacion(
                $camionId,
                $choferId,
                $guiaRutaId,
                $lat,
                $lng,
                $estado
            );
        } catch (\Throwable $e) {
            // Log del error pero retornar 200 para no bloquear el cliente
            Log::error("[GPS] Error al escribir ubicación en Firestore: {$e->getMessage()}", [
                'camion_id'   => $camionId,
                'chofer_id'   => $choferId,
                'lat'         => $lat,
                'lng'         => $lng,
            ]);
        }

        return response()->json([
            'ok'        => true,
            'camion_id' => $camionId,
            'message'   => 'Ubicación actualizada',
        ], 200);
    }
}
