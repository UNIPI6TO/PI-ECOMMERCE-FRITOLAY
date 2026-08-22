<?php
$host = '34.72.182.198';
$user = 'root';
$pass = ':|m-Lx4ym|QvPH?Z';
$db = 'fritolay';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT id, nombre, email FROM usuarios");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($users);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
