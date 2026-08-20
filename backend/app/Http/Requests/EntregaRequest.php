<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntregaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pedido_id' => 'required|integer|exists:pedidos,id',
            'items' => 'required|array|min:1',
            'items.*.item_pedido_id' => 'required|integer|exists:items_pedido,id',
            'items.*.cantidad_entregada' => 'required|integer|min:0',
            'items.*.cantidad_devuelta' => 'nullable|integer|min:0',
            'items.*.motivo_devolucion' => 'nullable|string|max:500',
            'items.*.estado_mercaderia' => 'nullable|in:buen_estado,mal_estado'
        ];
    }
}
