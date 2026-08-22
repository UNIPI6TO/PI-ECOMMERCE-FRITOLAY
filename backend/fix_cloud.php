<?php
$host = '34.72.182.198';
$user = 'root';
$pass = ':|m-Lx4ym|QvPH?Z';
$db = 'fritolay_db';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Truncate tables in new cloud database
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $pdo->exec("TRUNCATE TABLE direcciones_cliente;");
    $pdo->exec("TRUNCATE TABLE clientes;");
    $pdo->exec("TRUNCATE TABLE usuarios;");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    // Connect to local DB to get Wilson
    $local = new PDO("mysql:host=127.0.0.1;dbname=fritolay_db", "root", "root");
    $local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $local->query("SELECT * FROM usuarios WHERE id = 1");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $insert = $pdo->prepare("INSERT INTO usuarios (id, nombre, email, password_hash, rol, activo, recovery_pin_hash, recovery_pin_expires_at, creado_en, created_at, updated_at) VALUES (:id, :nombre, :email, :password_hash, :rol, :activo, :recovery_pin_hash, :recovery_pin_expires_at, :creado_en, :created_at, :updated_at)");
        
        $insert->execute([
            ':id' => $admin['id'],
            ':nombre' => $admin['nombre'],
            ':email' => $admin['email'],
            ':password_hash' => $admin['password_hash'],
            ':rol' => $admin['rol'],
            ':activo' => $admin['activo'],
            ':recovery_pin_hash' => $admin['recovery_pin_hash'],
            ':recovery_pin_expires_at' => $admin['recovery_pin_expires_at'],
            ':creado_en' => $admin['creado_en'],
            ':created_at' => $admin['created_at'],
            ':updated_at' => $admin['updated_at']
        ]);
        echo "Wilson Salinas migrated to CLOUD fritolay_db.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
