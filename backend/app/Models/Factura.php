<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factura extends Model
{
    protected $table = 'facturas';
    protected $fillable = ['pedido_id', 'numero_factura', 'fecha_emision', 'subtotal', 'iva', 'total'];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public static function generarNumero(int $id): string
    {
        $config = \App\Models\EmpresaConfig::get();
        $est = $config ? str_pad($config->codigo_establecimiento, 3, '0', STR_PAD_LEFT) : '003';
        $pto = $config ? str_pad($config->punto_emision, 3, '0', STR_PAD_LEFT) : '001';
        
        return $est . '-' . $pto . '-' . str_pad((string)$id, 9, '0', STR_PAD_LEFT);
    }

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }
}
