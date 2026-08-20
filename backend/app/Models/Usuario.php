<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $fillable = ['nombre', 'email', 'password_hash', 'rol', 'activo', 'recovery_pin_hash', 'recovery_pin_expires_at'];
    protected $hidden = ['password_hash', 'recovery_pin_hash'];
    protected $casts = [
        'activo' => 'boolean',
        'creado_en' => 'datetime',
        'recovery_pin_expires_at' => 'datetime'
    ];

    public const ROL_ADMINISTRADOR = 'administrador';
    public const ROL_OPERADOR = 'operador';
    public const ROL_CHOFER = 'chofer';
    public const ROL_CLIENTE = 'cliente';

    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'usuario_id');
    }

    public function auditorias(): HasMany
    {
        return $this->hasMany(BitacoraAuditoria::class, 'usuario_id');
    }

    public function camionAsignado(): HasOne
    {
        return $this->hasOne(Camion::class, 'chofer_id');
    }
}
