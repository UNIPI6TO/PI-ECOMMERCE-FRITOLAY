<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE asignacion_pedido_camion MODIFY COLUMN estado VARCHAR(50) NOT NULL DEFAULT 'asignado'");
        } catch (\Throwable $e) {
            // Silencioso si la columna ya es VARCHAR o la BD difiere
        }
    }

    public function down(): void
    {
    }
};
