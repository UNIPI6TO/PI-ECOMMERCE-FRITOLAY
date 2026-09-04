<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MercaderiaMalEstado extends Model
{
    protected $table = 'mercaderia_mal_estado';
    protected $fillable = ['guia_ruta_id', 'pedido_id', 'producto_id', 'cantidad', 'motivo', 'registrado_en', 'fecha_pedido'];

    protected $casts = [
        'cantidad' => 'float',
        'registrado_en' => 'datetime',
        'fecha_pedido' => 'datetime',
    ];

    public function guiaRuta(): BelongsTo
    {
        return $this->belongsTo(GuiaRuta::class, 'guia_ruta_id');
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
