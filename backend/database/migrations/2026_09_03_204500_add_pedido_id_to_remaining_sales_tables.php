<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'notas_credito',
            'mercaderia_mal_estado',
            'transacciones_inventario'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'pedido_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->unsignedBigInteger('pedido_id')->nullable()->after('id')->index("idx_{$table}_pedido_id");
                });
            }
        }

        // Backfill datos históricos
        DB::statement("
            UPDATE notas_credito nc 
            JOIN facturas f ON f.id = nc.factura_id 
            SET nc.pedido_id = f.pedido_id 
            WHERE nc.pedido_id IS NULL
        ");

        DB::statement("
            UPDATE mercaderia_mal_estado mme 
            JOIN guias_ruta gr ON gr.id = mme.guia_ruta_id 
            JOIN asignacion_pedido_camion apc ON apc.guia_ruta_id = gr.id 
            SET mme.pedido_id = apc.pedido_id 
            WHERE mme.pedido_id IS NULL
        ");

        DB::statement("
            UPDATE transacciones_inventario ti 
            JOIN items_pedido ip ON ip.producto_id = ti.producto_id AND DATE(ip.created_at) = DATE(ti.created_at)
            SET ti.pedido_id = ip.pedido_id 
            WHERE ti.pedido_id IS NULL
        ");
    }

    public function down(): void
    {
        $tables = [
            'notas_credito',
            'mercaderia_mal_estado',
            'transacciones_inventario'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'pedido_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->dropIndex("idx_{$table}_pedido_id");
                    $t->dropColumn('pedido_id');
                });
            }
        }
    }
};
