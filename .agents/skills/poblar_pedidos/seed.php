<?php
$numPedidos = isset($argv[1]) ? (int)$argv[1] : 20;

if ($numPedidos <= 0) {
    die("Error: El número de pedidos debe ser mayor a 0.\n");
}

$pdo = new PDO('mysql:host=127.0.0.1;dbname=fritolay_db;charset=utf8', 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$comprobanteHardcoded = 'gs://fritolay-images-project-3e1faa58-1e7d-4e8d-933/comprobantes/13/2026-08/pedido_19_1787882539.png';
$estados = ['en_espera_aprobacion']; // "pendiente" // "pendiente" de asignacion
$metodosPago = ['efectivo', 'deposito', 'de_una', 'tc', 'td'];

// 1. Fetch clients that HAVE an address
$stmt = $pdo->query("
    SELECT c.id as cliente_id, d.id as direccion_id 
    FROM clientes c
    JOIN direcciones_cliente d ON c.id = d.cliente_id
    WHERE d.latitud IS NOT NULL AND d.longitud IS NOT NULL
");
$clientesDirecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($clientesDirecciones)) {
    die("Error: No hay clientes con direcciones válidas en la base de datos.\n");
}

// 2. Fetch all products
$stmt = $pdo->query("SELECT id, precio FROM productos");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($productos)) {
    die("Error: No hay productos en la base de datos.\n");
}

echo "Generando $numPedidos pedidos de prueba...\n";

$pdo->beginTransaction();

try {
    for ($i = 0; $i < $numPedidos; $i++) {
        // Pick random client & address
        $cd = $clientesDirecciones[array_rand($clientesDirecciones)];
        
        // Randomize items (1 to 5 distinct products)
        shuffle($productos);
        $numItems = rand(1, 5);
        $items = array_slice($productos, 0, $numItems);
        
        $subtotal = 0;
        $orderItems = [];
        foreach ($items as $prod) {
            $cantidad = rand(1, 5);
            $precio = (float)$prod['precio'];
            $lineTotal = $cantidad * $precio;
            $subtotal += $lineTotal;
            
            $orderItems[] = [
                'producto_id' => $prod['id'],
                'cantidad' => $cantidad,
                'precio' => $precio,
                'total_linea' => $lineTotal
            ];
        }
        
        // Calculate taxes (15% IVA based on standard Ecuador logic for these products)
        $iva = $subtotal * 0.15; 
        $total = $subtotal + $iva;
        $descuento = 0;

        // Metodo de pago & Comprobante
        $metodoPago = $metodosPago[array_rand($metodosPago)];
        $comprobante = null;
        if (in_array($metodoPago, ['deposito', 'de_una'])) {
            $comprobante = $comprobanteHardcoded;
        }

        // Estado
        $estado = $estados[array_rand($estados)];

        // Date (spread out over the last 30 days, always in the past)
        $diasAtras = rand(1, 30);
        $horasAtras = rand(0, 23);
        $minutosAtras = rand(0, 59);
        $fecha = date('Y-m-d H:i:s', strtotime("-$diasAtras days -$horasAtras hours -$minutosAtras minutes"));

        // Insert Pedido
        $stmtInsertPedido = $pdo->prepare("
            INSERT INTO pedidos 
            (cliente_id, direccion_id, estado, metodo_pago, comprobante_path, subtotal, descuento, iva, total, creado_en) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmtInsertPedido->execute([
            $cd['cliente_id'],
            $cd['direccion_id'],
            $estado,
            $metodoPago,
            $comprobante,
            $subtotal,
            $descuento,
            $iva,
            $total,
            $fecha
        ]);
        
        $pedidoId = $pdo->lastInsertId();

        // Insert Items
        $stmtInsertItem = $pdo->prepare("
            INSERT INTO items_pedido 
            (pedido_id, producto_id, cantidad_solicitada, cantidad_entregada, precio_unitario, descuento_aplicado)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($orderItems as $item) {
            $stmtInsertItem->execute([
                $pedidoId,
                $item['producto_id'],
                $item['cantidad'],
                0, // cantidad_entregada starts at 0
                $item['precio'],
                0  // descuento
            ]);
        }
    }
    
    $pdo->commit();
    echo "¡Seed completado! $numPedidos pedidos insertados correctamente con sus ítems (Estado: en_espera_aprobacion (Pendiente)).\n";

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error durante la inserción: " . $e->getMessage() . "\n");
}
