<?php
$host = '34.72.182.198';
$user = 'root';
$pass = ':|m-Lx4ym|QvPH?Z';
$db = 'fritolay_db';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("UPDATE clientes JOIN usuarios ON clientes.usuario_id = usuarios.id SET clientes.nombre_cliente = usuarios.nombre WHERE clientes.nombre_cliente IS NULL;");
    $pdo->exec("UPDATE direcciones_cliente SET referencia = 'Sin referencia' WHERE referencia IS NULL;");
    echo "Cloud DB existing fields updated.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
