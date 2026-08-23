<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Limpiar datos de productos y relaciones para empezar de nuevo como pidió el usuario
        Schema::disableForeignKeyConstraints();
        DB::table('items_pedido')->truncate();
        DB::table('bodega_camion')->truncate();
        DB::table('mercaderia_mal_estado')->truncate();
        DB::table('transacciones_inventario')->truncate();
        DB::table('productos')->truncate();
        Schema::enableForeignKeyConstraints();

        Schema::table('productos', function (Blueprint $table) {
            $table->string('marca')->nullable()->after('descripcion');
            $table->string('categoria')->nullable()->after('marca');
            
            if (Schema::hasColumn('productos', 'tipo')) {
                $table->dropColumn('tipo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('tipo')->nullable();
            $table->dropColumn('marca');
            $table->dropColumn('categoria');
        });
    }
};
