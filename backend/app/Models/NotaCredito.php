<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaCredito extends Model
{
    protected $table = 'notas_credito';

    protected $fillable = [
        'factura_id',
        'pedido_id',
        'numero_nota',
        'fecha_emision',
        'valor_total',
        'motivo',
        'fecha_pedido',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public static function generarNumero(int $id): string
    {
        $config = \App\Models\EmpresaConfig::get();
        $est = $config ? str_pad((string)$config->codigo_establecimiento, 3, '0', STR_PAD_LEFT) : '003';
        $pto = $config ? str_pad((string)$config->punto_emision, 3, '0', STR_PAD_LEFT) : '001';
        
        return $est . '-' . $pto . '-' . str_pad((string)$id, 9, '0', STR_PAD_LEFT);
    }
}
