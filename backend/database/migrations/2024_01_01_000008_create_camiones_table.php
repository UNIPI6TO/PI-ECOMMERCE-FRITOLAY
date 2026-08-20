<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camiones', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 20)->unique();
            $table->string('descripcion')->nullable();
            $table->enum('estado', ['activo', 'mantenimiento', 'averia', 'inactivo'])->default('activo');
            $table->foreignId('chofer_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camiones');
    }
};
