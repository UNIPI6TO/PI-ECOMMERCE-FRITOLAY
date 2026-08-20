<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DescuentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => ['nullable', 'integer', 'exists:clientes,id'],
            'tipo' => ['required', 'in:individual,global'],
            'porcentaje' => ['required', 'numeric', 'min:0.01', 'max:100'],
            'metodo_pago' => ['required', 'in:efectivo,deposito,de_una,tc,td,todos'],
            'fecha_caducidad' => ['required', 'date', 'after:today'],
        ];
    }
}
