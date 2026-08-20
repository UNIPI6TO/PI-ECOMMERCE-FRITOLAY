<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Camion extends Model
{
    protected $table = 'camiones';
    protected $fillable = ['placa', 'descripcion', 'estado', 'chofer_id'];

    public const ESTADO_ACTIVO = 'activo';
    public const ESTADO_MANTENIMIENTO = 'mantenimiento';
    public const ESTADO_AVERIA = 'averia';
    public const ESTADO_INACTIVO = 'inactivo';

    public function chofer(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'chofer_id');
    }

    public function guiasRemision(): HasMany
    {
        return $this->hasMany(GuiaRemision::class, 'camion_id');
    }

    public function bodega(): HasMany
    {
        return $this->hasMany(BodegaCamion::class, 'camion_id');
    }

    public function transaccionesInventario(): HasMany
    {
        return $this->hasMany(TransaccionInventario::class, 'camion_id');
    }
}
