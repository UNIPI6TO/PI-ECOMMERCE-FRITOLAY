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

    public function subirComprobante(UploadedFile $file, int $pedidoId, int $clienteId, string $mesPedido): string
    {
        $prefix = "comprobantes/{$clienteId}/{$mesPedido}";
        $filename = "{$prefix}/pedido_{$pedidoId}_" . time() . '.' . $file->getClientOriginalExtension();
        
        $bucketName = config('fritolay.gcs_bucket_comprobantes');
        if (empty($bucketName)) {
            $bucketName = 'fritolay-images-project-3e1faa58-1e7d-4e8d-933';
        }
        
        try {
            $storage = new \Google\Cloud\Storage\StorageClient();
            $bucket = $storage->bucket($bucketName);
            
            $bucket->upload(
                fopen($file->getRealPath(), 'r'),
                ['name' => $filename]
            );
            return "https://storage.googleapis.com/{$bucketName}/{$filename}";
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error uploading to GCS: ' . $e->getMessage());
            // Fallback local en caso de no tener credenciales de GCP configuradas
            return $file->storeAs($prefix, basename($filename), 'local');
        }
    }

    public function getUrlFirmada(string $path, int $minutesTTL = 15): string
    {
        if (empty($path)) {
            return '';
        }
        
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $bucketName = config('fritolay.gcs_bucket_comprobantes', 'fritolay-images-project-3e1faa58-1e7d-4e8d-933');
        return "https://storage.googleapis.com/{$bucketName}/{$path}";
    }

    public function subirImagen(UploadedFile $file, string $nombre): string
    {
        if (App::environment('local')) {
            return $file->storeAs('imagenes', $nombre, 'local');
        }
        
        return Storage::disk('gcs')->putFileAs('imagenes', $file, $nombre);
    }
}
