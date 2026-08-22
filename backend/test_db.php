<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $affected = \Illuminate\Support\Facades\DB::table('pedidos')
        ->where('cliente_id', 6)
        ->update(['cliente_id' => 5]);
    echo "Actualizados: " . $affected;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
