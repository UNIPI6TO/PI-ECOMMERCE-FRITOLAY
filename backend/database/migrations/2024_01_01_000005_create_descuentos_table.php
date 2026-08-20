<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('descuentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
            $table->enum('tipo', ['individual', 'global']);
            $table->decimal('porcentaje', 5, 2);
            $table->enum('metodo_pago', ['efectivo', 'deposito', 'de_una', 'tc', 'td', 'todos']);
            $table->dateTime('fecha_caducidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descuentos');
    }
};
