<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuiaRuta extends Model
{
    protected $table = 'guias_ruta';
    protected $fillable = ['guia_remision_id', 'fecha_creacion', 'estado'];

    public const ESTADO_ACTIVA = 'activa';
    public const ESTADO_CERRADA = 'cerrada';

    protected $casts = [
        'fecha_creacion' => 'datetime',
    ];

    public function guiaRemision(): BelongsTo
    {
        return $this->belongsTo(GuiaRemision::class, 'guia_remision_id');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(AsignacionPedidoCamion::class, 'guia_ruta_id');
    }

    public function mercaderiaMalEstado(): HasMany
    {
        return $this->hasMany(MercaderiaMalEstado::class, 'guia_ruta_id');
    }
}
