<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nombre_cliente')->after('ruc_cedula')->nullable();
        });

        Schema::table('direcciones_cliente', function (Blueprint $table) {
            $table->text('referencia')->after('descripcion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('nombre_cliente');
        });

        Schema::table('direcciones_cliente', function (Blueprint $table) {
            $table->dropColumn('referencia');
        });
    }
};
