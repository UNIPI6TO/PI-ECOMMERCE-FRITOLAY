<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class PedidoFiltroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado' => 'nullable|in:carrito,en_espera_aprobacion,en_espera_asignacion,listo_para_entregar,en_ruta,entregado,entregado_parcialmente,cancelado',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('fecha_inicio') && $this->filled('fecha_fin')) {
                $inicio = Carbon::parse($this->input('fecha_inicio'));
                $fin = Carbon::parse($this->input('fecha_fin'));

                if ($inicio->diffInDays($fin) > 31) {
                    $validator->errors()->add('fechas', 'El rango máximo es de 31 días (1 mes)');
                }
            }
        });
    }
}
