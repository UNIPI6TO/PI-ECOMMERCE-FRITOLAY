<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->enum('rol', ['administrador', 'operador', 'chofer', 'cliente']);
            $table->boolean('activo')->default(true);
            $table->string('recovery_pin_hash')->nullable();
            $table->timestamp('recovery_pin_expires_at')->nullable();
            $table->timestamp('creado_en')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
