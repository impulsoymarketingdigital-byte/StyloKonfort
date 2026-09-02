<?php
require 'Config/Config.php';
require 'Config/App/Autoload.php';

echo "--- USUARIOS ---\n";
try {
    require_once 'Controllers/Usuarios.php';
    $u = new Usuarios();
    $u->listar();
} catch (Throwable $t) {
    echo $t->getMessage() . "\n";
}

echo "\n--- ALMACENES ---\n";
try {
    require_once 'Controllers/Almacenes.php';
    $a = new Almacenes();
    $a->listar();
} catch (Throwable $t) {
    echo $t->getMessage() . "\n";
}
