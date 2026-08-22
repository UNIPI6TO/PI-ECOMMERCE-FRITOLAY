<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $fillable = ['usuario_id', 'ruc_cedula', 'razon_social', 'telefono', 'nombre_cliente'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function direcciones(): HasMany
    {
        return $this->hasMany(DireccionCliente::class, 'cliente_id');
    }

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

    public function descuentos(): HasMany
    {
        return $this->hasMany(Descuento::class, 'cliente_id');
    }

    public function carritosAbandonados(): HasMany
    {
        return $this->hasMany(CarritoAbandonado::class, 'cliente_id');
    }
}
