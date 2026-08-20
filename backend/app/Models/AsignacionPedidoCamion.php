<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionPedidoCamion extends Model
{
    protected $table = 'asignacion_pedido_camion';
    protected $fillable = ['pedido_id', 'guia_ruta_id', 'orden', 'estado'];

    public const ESTADO_ASIGNADO = 'asignado';
    public const ESTADO_EN_RUTA = 'en_ruta';
    public const ESTADO_ENTREGADO = 'entregado';
    public const ESTADO_NO_ENTREGADO = 'no_entregado';

    protected $casts = [
        'orden' => 'integer',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function guiaRuta(): BelongsTo
    {
        return $this->belongsTo(GuiaRuta::class, 'guia_ruta_id');
    }
}
