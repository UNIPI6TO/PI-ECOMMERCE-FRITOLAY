<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $fillable = ['nombre', 'descripcion', 'precio', 'cantidad_fisica', 'tipo', 'estado', 'imagen_gcs_path', 'unidades_por_paca', 'en_pedidos'];
    
    protected $casts = [
        'precio' => 'decimal:2',
        'cantidad_fisica' => 'float',
        'en_pedidos' => 'float',
    ];

    public function getDisponibleAttribute(): float
    {
        return (float) ($this->cantidad_fisica - $this->en_pedidos);
    }

    public function estaAgotado(): bool
    {
        return $this->getDisponibleAttribute() <= 0;
    }

    public function stockBajoUmbral(int $umbral): bool
    {
        return $this->getDisponibleAttribute() < $umbral;
    }
}
