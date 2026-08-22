<?php
$host = '34.72.182.198';
$user = 'root';
$pass = ':|m-Lx4ym|QvPH?Z';
$oldDb = 'fritolay';
$newDb = 'fritolay_db';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch the admin user from old cloud database
    $stmt = $pdo->prepare("SELECT * FROM $oldDb.usuarios WHERE id = 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Truncate tables in new cloud database
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->exec("TRUNCATE TABLE $newDb.direcciones_cliente;");
        $pdo->exec("TRUNCATE TABLE $newDb.clientes;");
        $pdo->exec("TRUNCATE TABLE $newDb.usuarios;");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // Insert the admin user into the new cloud database
        $insert = $pdo->prepare("INSERT INTO $newDb.usuarios (id, nombre, email, password_hash, rol, activo, recovery_pin_hash, recovery_pin_expires_at, creado_en, created_at, updated_at) VALUES (:id, :nombre, :email, :password_hash, :rol, :activo, :recovery_pin_hash, :recovery_pin_expires_at, :creado_en, :created_at, :updated_at)");
        
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
        echo "Admin migrated successfully to cloud fritolay_db.\n";
    } else {
        echo "No admin found in old cloud db.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
