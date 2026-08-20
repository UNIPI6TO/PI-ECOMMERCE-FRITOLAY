<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsignacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pedido_ids' => 'required|array|min:1',
            'pedido_ids.*' => 'integer|exists:pedidos,id',
            'camion_id' => 'required|integer|exists:camiones,id'
        ];
    }
}
