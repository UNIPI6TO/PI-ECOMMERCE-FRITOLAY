<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\Storage::disk('gcs')->files();
    echo "GCS disk is configured!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
