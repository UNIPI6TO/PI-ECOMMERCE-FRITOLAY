<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuiaRemision extends Model
{
    protected $table = 'guias_remision';
    protected $fillable = ['camion_id', 'operador_id', 'fecha_generacion', 'estado', 'efectivo_declarado', 'revisada_por', 'fecha_revision'];

    public const ESTADO_ABIERTA = 'abierta';
    public const ESTADO_CONFIRMACION_CIERRE = 'confirmacion_cierre';
    public const ESTADO_CERRADA = 'cerrada';
    public const ESTADO_REVISADA = 'revisada';

    protected $casts = [
        'fecha_generacion' => 'datetime',
        'fecha_revision' => 'datetime',
        'efectivo_declarado' => 'decimal:2',
    ];

    public function camion(): BelongsTo
    {
        return $this->belongsTo(Camion::class, 'camion_id');
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'operador_id');
    }

    public function revisor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'revisada_por');
    }

    public function guiasRuta(): HasMany
    {
        return $this->hasMany(GuiaRuta::class, 'guia_remision_id');
    }
}
