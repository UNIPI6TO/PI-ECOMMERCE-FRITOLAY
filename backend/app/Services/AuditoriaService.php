<?php declare(strict_types=1);

namespace App\Services;
use App\Models\BitacoraAuditoria;

class AuditoriaService
{
    public function log(
        int $usuarioId,
        string $accion,
        string $tabla,
        int $registroId,
        ?array $antes = null,
        ?array $despues = null
    ): void {
        BitacoraAuditoria::create([
            'usuario_id' => $usuarioId,
            'accion' => $accion,
            'tabla_afectada' => $tabla,
            'registro_id' => $registroId,
            'datos_anteriores' => $antes,
            'datos_nuevos' => $despues,
            'fecha_accion' => now(),
        ]);
    }
}
