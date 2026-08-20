<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BodegaCamion extends Model
{
    protected $table = 'bodega_camion';
    protected $fillable = ['camion_id', 'producto_id', 'cantidad_actual'];

    protected $casts = [
        'cantidad_actual' => 'float',
    ];

    public function camion(): BelongsTo
    {
        return $this->belongsTo(Camion::class, 'camion_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
