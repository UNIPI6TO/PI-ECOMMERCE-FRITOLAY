<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

class GcsService
{
    private string $bucket;

    public function __construct()
    {
        $this->bucket = config('fritolay.gcs_bucket_comprobantes', 'default-bucket');
    }

    public function subirComprobante(UploadedFile $file, int $pedidoId): string
    {
        $filename = "comprobantes/pedido_{$pedidoId}_" . time() . '.' . $file->getClientOriginalExtension();
        
        if (App::environment('local')) {
            return $file->storeAs('comprobantes', basename($filename), 'local');
        }

        // Simulación GCS en producción
        return Storage::disk('gcs')->putFileAs('comprobantes', $file, basename($filename));
    }

    public function getUrlFirmada(string $path, int $minutesTTL = 15): string
    {
        if (App::environment('local')) {
            return Storage::disk('local')->url($path);
        }

        return Storage::disk('gcs')->temporaryUrl($path, now()->addMinutes($minutesTTL));
    }

    public function subirImagen(UploadedFile $file, string $nombre): string
    {
        if (App::environment('local')) {
            return $file->storeAs('imagenes', $nombre, 'local');
        }
        
        return Storage::disk('gcs')->putFileAs('imagenes', $file, $nombre);
    }
}
