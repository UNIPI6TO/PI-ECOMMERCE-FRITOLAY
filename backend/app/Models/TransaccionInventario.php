<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaccionInventario extends Model
{
    protected $table = 'transacciones_inventario';
    protected $fillable = ['producto_id', 'camion_id', 'tipo', 'cantidad', 'motivo', 'fecha_transaccion'];

    public const TIPO_INGRESO = 'ingreso';
    public const TIPO_EGRESO = 'egreso';

    protected $casts = [
        'cantidad' => 'float',
        'fecha_transaccion' => 'datetime',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function camion(): BelongsTo
    {
        return $this->belongsTo(Camion::class, 'camion_id');
    }
}
