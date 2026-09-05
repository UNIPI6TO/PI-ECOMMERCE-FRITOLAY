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
        'subtotal', 'descuento', 'iva', 'total', 'valor_entrega', 'motivo_cancelacion', 'creado_en'
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

    // Homologación Catálogo SRI Ecuador (Tabla 24 / Ficha Técnica Comprobantes Electrónicos)
    public const FORMAS_PAGO_SRI = [
        'efectivo' => [
            'codigo' => '01',
            'descripcion' => 'SIN UTILIZACION DEL SISTEMA FINANCIERO (EFECTIVO)'
        ],
        'deposito' => [
            'codigo' => '20',
            'descripcion' => 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO (DEPOSITO/TRANSFERENCIA)'
        ],
        'de_una' => [
            'codigo' => '20',
            'descripcion' => 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO (DE UNA / TRANSFERENCIA)'
        ],
        'tc' => [
            'codigo' => '19',
            'descripcion' => 'TARJETA DE CREDITO'
        ],
        'td' => [
            'codigo' => '16',
            'descripcion' => 'TARJETA DE DEBITO'
        ],
    ];

    public function getFormaPagoSri(): array
    {
        $metodo = strtolower((string) ($this->metodo_pago ?? 'efectivo'));
        return self::FORMAS_PAGO_SRI[$metodo] ?? self::FORMAS_PAGO_SRI['efectivo'];
    }

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'valor_entrega' => 'decimal:2',
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
