<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('direccion_id')->constrained('direcciones_cliente');
            $table->enum('estado', ['en_espera_aprobacion', 'en_espera_asignacion', 'listo_para_entregar', 'en_ruta', 'entregado', 'entregado_parcialmente', 'no_entregado', 'cancelado']);
            $table->enum('metodo_pago', ['efectivo', 'deposito', 'de_una', 'tc', 'td']);
            $table->string('comprobante_path')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('iva', 10, 2);
            $table->decimal('total', 10, 2);
            $table->text('motivo_cancelacion')->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
