<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPedido extends Model
{
    protected $table = 'items_pedido';
    protected $fillable = ['pedido_id', 'producto_id', 'nombre_producto', 'descripcion_producto', 'cantidad_solicitada', 'cantidad_entregada', 'precio_unitario', 'descuento_aplicado'];

    protected $casts = [
        'cantidad_solicitada' => 'integer',
        'cantidad_entregada' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento_aplicado' => 'decimal:2',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
