<?php
require_once 'Config/Config.php';
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $nuevaClave = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuarios SET correo = 'admin@stylokonfort.com', clave = ? WHERE id = 1");
    $stmt->execute([$nuevaClave]);
    
    echo "CONTRASEÑA Y CORREO ACTUALIZADOS CON EXITO!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
