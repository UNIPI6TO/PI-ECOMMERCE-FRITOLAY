<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $fillable = [
        'cliente_id', 'direccion_id', 'estado', 'metodo_pago', 'comprobante_path', 
        'subtotal', 'descuento', 'iva', 'total', 'motivo_cancelacion', 'creado_en'
    ];

    public const ESTADO_EN_ESPERA_APROBACION = 'en_espera_aprobacion';
    public const ESTADO_EN_ESPERA_ASIGNACION = 'en_espera_asignacion';
    public const ESTADO_LISTO_PARA_ENTREGAR = 'listo_para_entregar';
    public const ESTADO_EN_RUTA = 'en_ruta';
    public const ESTADO_ENTREGADO = 'entregado';
    public const ESTADO_ENTREGADO_PARCIALMENTE = 'entregado_parcialmente';
    public const ESTADO_NO_ENTREGADO = 'no_entregado';
    public const ESTADO_CANCELADO = 'cancelado';

    public const PAGO_EFECTIVO = 'efectivo';
    public const PAGO_DEPOSITO = 'deposito';
    public const PAGO_DE_UNA = 'de_una';
    public const PAGO_TC = 'tc';
    public const PAGO_TD = 'td';

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'creado_en' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function direccion(): BelongsTo
    {
        return $this->belongsTo(DireccionCliente::class, 'direccion_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemPedido::class, 'pedido_id');
    }

    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class, 'pedido_id');
    }
}
