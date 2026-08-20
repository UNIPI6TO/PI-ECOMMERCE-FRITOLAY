<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bodega_camion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camion_id')->constrained('camiones');
            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('cantidad_actual', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['camion_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bodega_camion');
    }
};
