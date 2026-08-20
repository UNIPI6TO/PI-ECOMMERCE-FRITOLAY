<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['nullable', 'string'],
            'order_by' => ['nullable', 'string', 'in:nombre,precio,tipo'],
            'orden' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }
}
