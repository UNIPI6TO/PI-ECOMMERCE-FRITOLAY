<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaCredito extends Model
{
    protected $table = 'notas_credito';

    protected $fillable = [
        'factura_id',
        'numero_nota',
        'fecha_emision',
        'valor_total',
        'motivo',
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public static function generarNumero(int $facturaId)
    {
        $year = date('Y');
        $paddedId = str_pad($facturaId, 6, '0', STR_PAD_LEFT);
        return "NC-{$year}-{$paddedId}";
    }
}
