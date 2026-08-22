<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $facturas = App\Models\Factura::all();
    $count = 0;
    foreach ($facturas as $factura) {
        $factura->numero_factura = '001-001-' . str_pad((string)$factura->pedido_id, 9, '0', STR_PAD_LEFT);
        $factura->save();
        $count++;
    }
    echo "Facturas actualizadas a formato EC: " . $count;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
