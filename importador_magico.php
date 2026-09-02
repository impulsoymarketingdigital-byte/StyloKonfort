<?php
require 'Config/Config.php';
require 'Config/App/Conexion.php';

try {
    $conexion = new Conexion();
    $pdo = $conexion->conect();
    
    if (!$pdo) {
        die("Error: No se pudo conectar a la base de datos.");
    }

    echo "<h3>Conexión exitosa a la base de datos.</h3>";

    // Importar el backup principal
    if (file_exists('ecommerce (5).sql')) {
        $sql = file_get_contents('ecommerce (5).sql');
        $pdo->exec($sql);
        echo "<p>Backup principal (ecommerce 5) restaurado con éxito.</p>";
    } else {
        echo "<p>No se encontró el archivo ecommerce (5).sql</p>";
    }
    
    // Importar los arreglos de seguridad de hoy
    if (file_exists('migrations/001_base_fixes.sql')) {
        $sql_fixes = file_get_contents('migrations/001_base_fixes.sql');
        $queries = explode(';', $sql_fixes);
        foreach ($queries as $query) {
            if (trim($query)) {
                $pdo->exec($query);
            }
        }
        echo "<p>Parches de seguridad y cuentas admin aplicadas con éxito.</p>";
    }
    
    echo "<h2>¡TODO LISTO! 🎉 Ya puedes entrar a stylokonfort.com y ver tu tienda.</h2>";

} catch (Exception $e) {
    echo "<h2>HUBO UN ERROR:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
