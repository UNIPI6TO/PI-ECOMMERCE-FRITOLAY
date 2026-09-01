<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=fritolay_db;charset=utf8', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tables = [
    'notas_credito',
    'facturas',
    'mercaderia_mal_estado',
    'asignacion_pedido_camion',
    'items_pedido',
    'pedidos',
    'carritos_abandonados',
    'guias_ruta',
    'guias_remision',
    'bodega_camion',
    'transacciones_inventario'
];

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
foreach ($tables as $table) {
    try {
        $pdo->exec("TRUNCATE TABLE `$table`");
        echo "Tabla $table limpiada.\n";
    } catch (Exception $e) {
        echo "Omitiendo $table: " . $e->getMessage() . "\n";
    }
}
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
echo "Todas las tablas del proceso de ventas limpiadas con exito.\n";
