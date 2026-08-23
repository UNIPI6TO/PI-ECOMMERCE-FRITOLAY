<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$productos = App\Models\Producto::where('nombre', 'like', '%Doritos%')->get();
foreach ($productos as $p) {
    echo "ID: {$p->id} | Nombre: {$p->nombre} | Imagen: {$p->imagen_gcs_path}\n";
}
