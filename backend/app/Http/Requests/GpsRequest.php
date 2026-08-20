<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GpsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'camion_id' => 'required|integer|exists:camiones,id',
            'guia_ruta_id' => 'required|integer|exists:guias_ruta,id',
            'latitud' => 'required|numeric|between:-90,90',
            'longitud' => 'required|numeric|between:-180,180',
            'estado' => 'required|in:en_movimiento,detenido,entregando'
        ];
    }
}
