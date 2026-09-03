<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('catalogo_motivos')) {
            Schema::create('catalogo_motivos', function (Blueprint $table) {
                $table->id();
                $table->enum('tipo', ['abandono', 'cancelacion', 'devolucion', 'todos']);
                $table->string('descripcion');
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });

            // Poblar catálogo de motivos predefinidos estandarizados
            DB::table('catalogo_motivos')->insert([
                // Carritos Abandonados
                ['tipo' => 'abandono', 'descripcion' => 'Encontré mejor precio', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'abandono', 'descripcion' => 'Costo de envío alto', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'abandono', 'descripcion' => 'Decidí comprar después', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'abandono', 'descripcion' => 'Error en el pedido', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'abandono', 'descripcion' => 'Problemas con método de pago', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'abandono', 'descripcion' => 'Otro', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],

                // Cancelación de Pedidos
                ['tipo' => 'cancelacion', 'descripcion' => 'Cambio de opinión', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'cancelacion', 'descripcion' => 'Error en dirección o datos', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'cancelacion', 'descripcion' => 'Precio muy alto', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'cancelacion', 'descripcion' => 'Tiempo de entrega muy largo', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'cancelacion', 'descripcion' => 'Producto equivocado', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'cancelacion', 'descripcion' => 'Otro', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],

                // Devolución / No Entrega
                ['tipo' => 'devolucion', 'descripcion' => 'Local Cerrado', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'devolucion', 'descripcion' => 'Dirección Inválida', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'devolucion', 'descripcion' => 'Cliente Ausente', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'devolucion', 'descripcion' => 'Pedido Cancelado por Cliente', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'devolucion', 'descripcion' => 'Mercadería Rechazada / Mal Estado', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
                ['tipo' => 'devolucion', 'descripcion' => 'Otro', 'activo' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_motivos');
    }
};
