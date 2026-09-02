<?php
require_once 'Config/Config.php';
require_once 'Config/App/Autoload.php';
require_once 'Config/Helpers.php';
require_once 'Controllers/Usuarios.php';

session_start();
$_SESSION['id_usuario'] = 1;
$_SESSION['id_sucursal'] = 1;

$u = new Usuarios();
ob_start();
$u->listar();
$json = ob_get_clean();

echo "Raw Output:\n";
var_dump($json);
