<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FirestoreGpsService
{
    private bool $hasCredentials;

    public function __construct()
    {
        $this->hasCredentials = env('FIREBASE_CREDENTIALS_PATH') !== null && file_exists(env('FIREBASE_CREDENTIALS_PATH'));
    }

    public function escribirUbicacion(int $camionId, int $choferId, int $guiaRutaId, float $lat, float $lng, string $estado): void
    {
        if (app()->environment('production') && $this->hasCredentials) {
            // Logic with kreait/firebase-php
        } else {
            Log::info("GPS: Ubicación actualizada para camión $camionId. Lat: $lat, Lng: $lng");
        }
    }

    public function getUltimaUbicacion(int $camionId): ?array
    {
        return null;
    }
}
