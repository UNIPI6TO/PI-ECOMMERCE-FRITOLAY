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
        if (!Schema::hasColumn('direcciones_cliente', 'estado')) {
            Schema::table('direcciones_cliente', function (Blueprint $table) {
                $table->boolean('estado')->default(true)->after('es_por_defecto');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('direcciones_cliente', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
