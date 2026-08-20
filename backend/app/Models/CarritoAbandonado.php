<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarritoAbandonado extends Model
{
    protected $table = 'carritos_abandonados';
    protected $fillable = ['cliente_id', 'motivo_cancelacion', 'valor_total', 'fecha_abandono'];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'fecha_abandono' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
