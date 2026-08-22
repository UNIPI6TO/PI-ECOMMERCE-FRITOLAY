<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/api/pedidos', 'POST', [
    'items' => [
        [
            'producto_id' => '1',
            'cantidad' => '40'
        ]
    ],
    'direccion_id' => '1',
    'metodo_pago' => 'efectivo',
    'total' => '69'
]);
$request->headers->set('Accept', 'application/json');
$request->merge(['user_id' => '1']); // Fake JWT payload

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";
