<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoMotivo extends Model
{
    protected $table = 'catalogo_motivos';

    protected $fillable = [
        'tipo',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
