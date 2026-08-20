<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Descuento extends Model
{
    protected $table = 'descuentos';
    protected $fillable = ['cliente_id', 'tipo', 'porcentaje', 'metodo_pago', 'fecha_caducidad'];

    public const TIPO_INDIVIDUAL = 'individual';
    public const TIPO_GLOBAL = 'global';

    public const METODO_EFECTIVO = 'efectivo';
    public const METODO_DEPOSITO = 'deposito';
    public const METODO_DE_UNA = 'de_una';
    public const METODO_TC = 'tc';
    public const METODO_TD = 'td';
    public const METODO_TODOS = 'todos';

    protected $casts = [
        'porcentaje' => 'decimal:2',
        'fecha_caducidad' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function estaVigente(): bool
    {
        return $this->fecha_caducidad && $this->fecha_caducidad->isFuture();
    }
}
