<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('empresa_config')) {
            Schema::create('empresa_config', function (Blueprint $table) {
                $table->id();
                $table->string('razon_social', 200);
                $table->string('nombre_comercial', 200)->nullable();
                $table->string('ruc', 13);
                $table->string('codigo_establecimiento', 3)->default('003');
                $table->string('punto_emision', 3)->default('001');
                $table->string('direccion_matriz', 300);
                $table->string('direccion_sucursal', 300)->nullable();
                $table->string('telefono', 20)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('tipo_contribuyente', 100)->default('ESPECIAL');
                $table->boolean('obligado_contabilidad')->default(true);
                $table->string('tipo_ambiente', 1)->default('1')->comment('1=Pruebas, 2=Produccion');
                $table->string('tipo_emision', 1)->default('1')->comment('1=Normal');
                $table->string('logo_url', 500)->nullable();
                $table->string('color_primario', 7)->default('#E3001B');
                $table->timestamps();
            });

            // Insertar datos de Frito-Lay Ambato (PepsiCo)
            DB::table('empresa_config')->insert([
                'razon_social'           => 'Pepsico Alimentos Ecuador Cia. Ltda.',
                'nombre_comercial'       => 'Fritolay Ambato',
                'ruc'                    => '1790205401001',
                'codigo_establecimiento' => '003',
                'punto_emision'          => '001',
                'direccion_matriz'       => 'Av. General Rumiahui Lote 2, Sangolqu, Pichincha',
                'direccion_sucursal'     => 'Zona Industrial de Ambato, Tungurahua, Ecuador',
                'telefono'               => '032-000-000',
                'email'                  => 'facturacion@fritolay.com.ec',
                'tipo_contribuyente'     => 'ESPECIAL',
                'obligado_contabilidad'  => true,
                'tipo_ambiente'          => '1',
                'tipo_emision'           => '1',
                'logo_url'               => null,
                'color_primario'         => '#E3001B',
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_config');
    }
};
