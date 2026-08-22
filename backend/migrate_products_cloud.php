<?php
$cloudHost = '34.72.182.198';
$cloudUser = 'root';
$cloudPass = ':|m-Lx4ym|QvPH?Z';
$cloudDb = 'fritolay_db';

try {
    // Connect to local DB
    $local = new PDO("mysql:host=127.0.0.1;dbname=fritolay_db", "root", "root");
    $local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $local->query("SELECT * FROM productos");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($productos) {
        // Connect to Cloud DB
        $cloud = new PDO("mysql:host=$cloudHost;dbname=$cloudDb", $cloudUser, $cloudPass);
        $cloud->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $insert = $cloud->prepare("INSERT INTO productos (id, nombre, tipo, descripcion, precio, imagen_gcs_path, cantidad_fisica, en_pedidos, created_at, updated_at) VALUES (:id, :nombre, :tipo, :descripcion, :precio, :imagen_gcs_path, :cantidad_fisica, :en_pedidos, :created_at, :updated_at) ON DUPLICATE KEY UPDATE nombre=VALUES(nombre)");
        
        foreach ($productos as $p) {
            $insert->execute([
                ':id' => $p['id'],
                ':nombre' => $p['nombre'],
                ':tipo' => $p['tipo'],
                ':descripcion' => $p['descripcion'],
                ':precio' => $p['precio'],
                ':imagen_gcs_path' => $p['imagen_gcs_path'],
                ':cantidad_fisica' => $p['cantidad_fisica'],
                ':en_pedidos' => $p['en_pedidos'],
                ':created_at' => $p['created_at'],
                ':updated_at' => $p['updated_at']
            ]);
        }
        echo count($productos) . " products migrated to CLOUD fritolay_db.\n";
    } else {
        echo "No products found in local db.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
