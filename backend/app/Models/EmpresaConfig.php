<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresaConfig extends Model
{
    protected $table = 'empresa_config';

    protected $fillable = [
        'razon_social',
        'nombre_comercial',
        'ruc',
        'codigo_establecimiento',
        'punto_emision',
        'direccion_matriz',
        'direccion_sucursal',
        'telefono',
        'email',
        'tipo_contribuyente',
        'obligado_contabilidad',
        'tipo_ambiente',
        'tipo_emision',
        'logo_url',
        'color_primario',
    ];

    protected $casts = [
        'obligado_contabilidad' => 'boolean',
    ];

    /**
     * Devuelve siempre el único registro de configuración.
     */
    public static function get(): ?self
    {
        return static::first();
    }
}
