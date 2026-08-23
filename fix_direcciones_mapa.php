<?php
$databases = [
    ['host' => '127.0.0.1', 'port' => '3306', 'dbname' => 'fritolay_db', 'user' => 'root', 'pass' => 'root'],
    ['host' => '34.72.182.198', 'port' => '3306', 'dbname' => 'fritolay_db', 'user' => 'root', 'pass' => ':|m-Lx4ym|QvPH?Z']
];

foreach ($databases as $db) {
    try {
        echo "Actualizando direcciones en {$db['host']}...\n";
        $pdo = new PDO("mysql:host={$db['host']};port={$db['port']};dbname={$db['dbname']}", $db['user'], $db['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $pdo->query("SELECT id, latitud, longitud FROM direcciones_cliente WHERE descripcion NOT LIKE '%Ecuador%'");
        $direcciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Encontradas " . count($direcciones) . " direcciones para actualizar.\n";
        
        $updateStmt = $pdo->prepare("UPDATE direcciones_cliente SET descripcion = ? WHERE id = ?");
        
        foreach ($direcciones as $i => $dir) {
            $lat = $dir['latitud'];
            $lon = $dir['longitud'];
            
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}";
            
            $options = [
                'http' => [
                    'method' => "GET",
                    'header' => "User-Agent: FritolayApp/1.0\r\n"
                ]
            ];
            $context = stream_context_create($options);
            
            $response = file_get_contents($url, false, $context);
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['display_name'])) {
                    $updateStmt->execute([$data['display_name'], $dir['id']]);
                    echo "ID {$dir['id']}: {$data['display_name']}\n";
                }
            }
            
            // Nominatim policy: max 1 request per second
            usleep(1100000); 
        }
        
    } catch (Exception $e) {
        echo "Error en {$db['host']}: " . $e->getMessage() . "\n";
    }
}
