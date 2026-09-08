<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * GpsRequest — Validación de coordenadas GPS enviadas por el chofer.
 *
 * Acepta peticiones de:
 *  - Capacitor BackgroundGeolocation (nativo: Android/iOS)
 *  - gps-tracker.js (browser fallback)
 *
 * Campos:
 *  - camion_id    (int, requerido)
 *  - guia_ruta_id (int, opcional — puede ser 0 si no se conoce aún)
 *  - latitud      (float, requerido, rango GPS válido)
 *  - longitud     (float, requerido, rango GPS válido)
 *  - estado       (string, requerido — en_movimiento | detenido | entregando)
 */
class GpsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización por rol se hace en la ruta (middleware role:chofer)
    }

    public function rules(): array
    {
        return [
            'camion_id'    => 'required|integer|exists:camiones,id',
            // guia_ruta_id es opcional: el tracker nativo puede enviar 0 si aún no se asignó guía
            'guia_ruta_id' => 'nullable|integer',
            'latitud'      => 'required|numeric|between:-90,90',
            'longitud'     => 'required|numeric|between:-180,180',
            'estado'       => 'required|string|in:en_movimiento,detenido,entregando',
        ];
    }

    public function messages(): array
    {
        return [
            'camion_id.required' => 'El ID del camión es obligatorio.',
            'camion_id.exists'   => 'El camión especificado no existe.',
            'latitud.between'    => 'La latitud debe estar entre -90 y 90.',
            'longitud.between'   => 'La longitud debe estar entre -180 y 180.',
            'estado.in'          => 'El estado debe ser: en_movimiento, detenido o entregando.',
        ];
    }
}
