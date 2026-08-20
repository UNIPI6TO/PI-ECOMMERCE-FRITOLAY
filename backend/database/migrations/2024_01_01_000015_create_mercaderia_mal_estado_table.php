<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mercaderia_mal_estado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guia_ruta_id')->constrained('guias_ruta');
            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('cantidad', 10, 2);
            $table->text('motivo');
            $table->timestamp('registrado_en')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mercaderia_mal_estado');
    }
};
