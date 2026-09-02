<?php
// PARCHE TEMPORAL DE BASE DE DATOS
try {
    require_once 'Config/Config.php';
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Agregar precio_mayorista a productos si no existe
    $pdo->exec("ALTER TABLE productos ADD COLUMN IF NOT EXISTS precio_mayorista decimal(10,2) DEFAULT 0.00 AFTER precio_venta");
    
    // 2. Agregar id_almacen a usuarios si no existe
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS id_almacen int(11) DEFAULT 1 AFTER id_sucursal");
    
    // 3. Llenar id_almacen con los datos de id_sucursal si estn vacos
    $pdo->exec("UPDATE usuarios SET id_almacen = id_sucursal WHERE id_almacen IS NULL OR id_almacen = 1");

} catch (PDOException $e) {
    // ignorar silenciosamente
}
<?php

require_once 'Config/Config.php';
$ruta = !empty($_GET['url']) ? $_GET['url'] : "home/index";
$array = explode("/", $ruta);
$controller = ucfirst($array[0]);
$metodo = "index";
$parametro = "";
if (!empty($array[1])) {
    if (!empty($array[1] != "")) {
        $metodo = $array[1];
    }
}
if (!empty($array[2])) {
    if (!empty($array[2] != "")) {
        for ($i = 2; $i < count($array); $i++) {
            $parametro .= $array[$i] . ",";
        }
        $parametro = trim($parametro, ",");
    }
}
require_once 'Config/App/Autoload.php';
require_once 'Config/Helpers.php';
$dirControllers = "Controllers/" . $controller . ".php";
if (file_exists($dirControllers)) {
    require_once $dirControllers;
    $controller = new $controller();
    if (method_exists($controller, $metodo)) {
        $controller->$metodo($parametro);
    } else {
        header('Location: '.BASE_URL.'errors');
    }
} else {
    header('Location: ' . BASE_URL . 'errors');
}
?>

