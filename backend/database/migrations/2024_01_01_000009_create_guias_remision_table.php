<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guias_remision', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camion_id')->constrained('camiones');
            $table->foreignId('operador_id')->constrained('usuarios');
            $table->timestamp('fecha_generacion')->useCurrent();
            $table->enum('estado', ['abierta', 'confirmacion_cierre', 'cerrada'])->default('abierta');
            $table->decimal('efectivo_declarado', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guias_remision');
    }
};
