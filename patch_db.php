<?php
require_once 'Config/Config.php';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add precio_mayorista to productos
    try {
        $pdo->exec("ALTER TABLE productos ADD COLUMN precio_mayorista decimal(10,2) DEFAULT 0.00 AFTER precio_venta");
        echo "Columna precio_mayorista agregada.\n";
    } catch (PDOException $e) {
        echo "Info: " . $e->getMessage() . "\n";
    }

    // Check if almacenes exists, if not, create it or alias it
    // Wait, let's just see what the error is for usuarios!
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
