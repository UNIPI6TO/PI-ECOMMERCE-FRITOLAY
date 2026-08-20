<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngresoInventarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id' => 'required|integer|exists:productos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'required|string|max:255'
        ];
    }
}
