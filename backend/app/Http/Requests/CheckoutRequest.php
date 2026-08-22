<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'direccion_id' => ['required', 'integer', 'exists:direcciones_cliente,id'],
            'metodo_pago' => ['required', 'in:efectivo,deposito,de_una,tc,td'],
            'comprobante' => [
                'required_if:metodo_pago,deposito',
                'required_if:metodo_pago,de_una',
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120'
            ],
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Illuminate\Support\Facades\Log::error('Validation failed in CheckoutRequest', $validator->errors()->toArray());
        parent::failedValidation($validator);
    }
}
