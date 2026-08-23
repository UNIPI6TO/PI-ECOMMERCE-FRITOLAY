<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename Doritos
        DB::table('productos')
            ->where('nombre', 'like', '%Doritos Limón%')
            ->update([
                'nombre' => 'Doritos Lemon Remix',
                'descripcion' => 'Doritos de Lemon Remix',
                'imagen_gcs_path' => 'https://storage.googleapis.com/fritolay-images-project-3e1faa58-1e7d-4e8d-933/doritos_lemon_remix.png'
            ]);

        // 2. Rename Ruffles Limon -> Ruffles Twist
        DB::table('productos')
            ->where('nombre', 'like', '%Ruffles Limón%')
            ->update([
                'nombre' => 'Ruffles Twist',
                'descripcion' => 'Ruffles Twist sabor Limón',
                'imagen_gcs_path' => 'https://storage.googleapis.com/fritolay-images-project-3e1faa58-1e7d-4e8d-933/ruffles_twist.png'
            ]);

        // 3. Rename Ruffles Cebolla -> Ruffles Crema y Cebolla
        DB::table('productos')
            ->where('nombre', 'like', '%Ruffles Cebolla%')
            ->update([
                'nombre' => 'Ruffles Crema y Cebolla',
                'descripcion' => 'Ruffles sabor a Crema y Cebolla',
                'imagen_gcs_path' => 'https://storage.googleapis.com/fritolay-images-project-3e1faa58-1e7d-4e8d-933/ruffles_crema_cebolla.png'
            ]);

        // 4. Add K-chitos Picantes
        DB::table('productos')->updateOrInsert(
            ['nombre' => 'K-chitos Picantes'],
            [
                'descripcion' => 'Snack de maíz con sabor picante',
                'precio' => 0.50,
                'cantidad_fisica' => 100,
                'en_pedidos' => 0,
                'tipo' => 'Snack',
                'imagen_gcs_path' => 'https://via.placeholder.com/300x400?text=K-chitos+Picantes',
                'unidades_por_paca' => 12
            ]
        );

        // 5. Add K-chitos Naturales
        DB::table('productos')->updateOrInsert(
            ['nombre' => 'K-chitos Naturales'],
            [
                'descripcion' => 'Snack de maíz con sabor natural',
                'precio' => 0.50,
                'cantidad_fisica' => 100,
                'en_pedidos' => 0,
                'tipo' => 'Snack',
                'imagen_gcs_path' => 'https://via.placeholder.com/300x400?text=K-chitos+Naturales',
                'unidades_por_paca' => 12
            ]
        );

        // 6. Add Lays Artesanas Dinamita
        DB::table('productos')->updateOrInsert(
            ['nombre' => 'Lays Artesanas Dinamita'],
            [
                'descripcion' => 'Papas fritas Lays Artesanas corte grueso sabor Dinamita',
                'precio' => 0.80,
                'cantidad_fisica' => 100,
                'en_pedidos' => 0,
                'tipo' => 'Papas',
                'imagen_gcs_path' => 'https://via.placeholder.com/300x400?text=Lays+Dinamita',
                'unidades_por_paca' => 12
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('productos')
            ->where('nombre', 'Doritos Lemon Remix')
            ->update([
                'nombre' => 'Doritos Limón 150g',
                'descripcion' => 'Doritos sabor a limón',
                'imagen_gcs_path' => 'https://storage.googleapis.com/fritolay-images-project-3e1faa58-1e7d-4e8d-933/doritos_limon.png'
            ]);

        DB::table('productos')
            ->where('nombre', 'Ruffles Twist')
            ->update([
                'nombre' => 'Ruffles Limón 150g',
                'descripcion' => 'Ruffles sabor a limón',
                'imagen_gcs_path' => 'https://storage.googleapis.com/fritolay-images-project-3e1faa58-1e7d-4e8d-933/ruffles_limon.png'
            ]);

        DB::table('productos')
            ->where('nombre', 'Ruffles Crema y Cebolla')
            ->update([
                'nombre' => 'Ruffles Cebolla 150g',
                'descripcion' => 'Ruffles sabor a cebolla',
                'imagen_gcs_path' => 'https://storage.googleapis.com/fritolay-images-project-3e1faa58-1e7d-4e8d-933/ruffles_cebolla.png'
            ]);

        DB::table('productos')
            ->whereIn('nombre', ['K-chitos Picantes', 'K-chitos Naturales', 'Lays Artesanas Dinamita'])
            ->delete();
    }
};
