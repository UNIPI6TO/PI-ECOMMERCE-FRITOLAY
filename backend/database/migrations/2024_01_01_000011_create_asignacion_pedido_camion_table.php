<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignacion_pedido_camion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos');
            $table->foreignId('guia_ruta_id')->constrained('guias_ruta');
            $table->integer('orden');
            $table->enum('estado', ['asignado', 'en_ruta', 'entregado', 'no_entregado'])->default('asignado');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacion_pedido_camion');
    }
};
