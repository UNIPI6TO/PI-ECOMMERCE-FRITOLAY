<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MalEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guia_ruta_id' => 'required|integer|exists:guias_ruta,id',
            'producto_id' => 'required|integer|exists:productos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:255'
        ];
    }
}
