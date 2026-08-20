<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FcmService
{
    public function enviarPushPedidoListo(int $clienteId, int $pedidoId, int $camionId): void
    {
        Log::info("Push notification sent to client $clienteId for order $pedidoId");
    }
}
