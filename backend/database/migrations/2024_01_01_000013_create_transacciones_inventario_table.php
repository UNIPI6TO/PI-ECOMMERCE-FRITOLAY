<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transacciones_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('camion_id')->nullable()->constrained('camiones');
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->decimal('cantidad', 10, 2);
            $table->string('motivo');
            $table->timestamp('fecha_transaccion')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacciones_inventario');
    }
};
