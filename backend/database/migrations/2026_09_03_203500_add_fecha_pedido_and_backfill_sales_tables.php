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
            'facturas',
            'notas_credito',
            'items_pedido',
            'asignacion_pedido_camion',
            'mercaderia_mal_estado',
            'transacciones_inventario'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'fecha_pedido')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->dateTime('fecha_pedido')->nullable()->after('id')->index("idx_{$table}_fecha_pedido");
                });
            }
        }

        // Backfill datos históricos
        DB::statement("
            UPDATE facturas f 
            JOIN pedidos p ON p.id = f.pedido_id 
            SET f.fecha_pedido = COALESCE(p.creado_en, p.created_at) 
            WHERE f.fecha_pedido IS NULL
        ");

        DB::statement("
            UPDATE notas_credito nc 
            JOIN facturas f ON f.id = nc.factura_id 
            JOIN pedidos p ON p.id = f.pedido_id 
            SET nc.fecha_pedido = COALESCE(p.creado_en, p.created_at) 
            WHERE nc.fecha_pedido IS NULL
        ");

        DB::statement("
            UPDATE items_pedido ip 
            JOIN pedidos p ON p.id = ip.pedido_id 
            SET ip.fecha_pedido = COALESCE(p.creado_en, p.created_at) 
            WHERE ip.fecha_pedido IS NULL
        ");

        DB::statement("
            UPDATE asignacion_pedido_camion apc 
            JOIN pedidos p ON p.id = apc.pedido_id 
            SET apc.fecha_pedido = COALESCE(p.creado_en, p.created_at) 
            WHERE apc.fecha_pedido IS NULL
        ");

        DB::statement("
            UPDATE mercaderia_mal_estado mme 
            JOIN guias_ruta gr ON gr.id = mme.guia_ruta_id 
            JOIN asignacion_pedido_camion apc ON apc.guia_ruta_id = gr.id 
            JOIN pedidos p ON p.id = apc.pedido_id 
            SET mme.fecha_pedido = COALESCE(p.creado_en, p.created_at) 
            WHERE mme.fecha_pedido IS NULL
        ");

        DB::statement("
            UPDATE mercaderia_mal_estado 
            SET fecha_pedido = registrado_en 
            WHERE fecha_pedido IS NULL
        ");

        DB::statement("
            UPDATE transacciones_inventario 
            SET fecha_pedido = fecha_transaccion 
            WHERE fecha_pedido IS NULL
        ");
    }

    public function down(): void
    {
        $tables = [
            'facturas',
            'notas_credito',
            'items_pedido',
            'asignacion_pedido_camion',
            'mercaderia_mal_estado',
            'transacciones_inventario'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'fecha_pedido')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->dropIndex("idx_{$table}_fecha_pedido");
                    $t->dropColumn('fecha_pedido');
                });
            }
        }
    }
};
