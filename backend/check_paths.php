<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$prods = Illuminate\Support\Facades\DB::table('productos')
    ->where('nombre', 'like', 'K-Chitos%')
    ->orWhere('nombre', 'like', 'Doritos Maíz%')
    ->orWhere('nombre', 'like', 'Doritos Mega%')
    ->get(['nombre', 'imagen_gcs_path']);

foreach($prods as $p) {
    echo $p->nombre . ' => ' . $p->imagen_gcs_path . "\n";
}
