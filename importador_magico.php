<?php
require 'Config/Config.php';

try {
    // Intentar conectar manualmente para ver el error real
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>Conexión exitosa a la base de datos.</h3>";

    if (file_exists('ecommerce (5).sql')) {
        $sql = file_get_contents('ecommerce (5).sql');
        $pdo->exec($sql);
        echo "<p>Backup principal (ecommerce 5) restaurado con éxito.</p>";
    } else {
        echo "<p>No se encontró el archivo ecommerce (5).sql</p>";
    }
    
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
    echo "<h2>ERROR DE CONEXIÓN A LA BASE DE DATOS:</h2>";
    echo "<p style='color:red;'><b>" . $e->getMessage() . "</b></p>";
    echo "<h3>Variables que estamos usando:</h3>";
    echo "<ul>";
    echo "<li>DB_HOST: " . DB_HOST . "</li>";
    echo "<li>DB_USER: " . DB_USER . "</li>";
    echo "<li>DB_NAME: " . DB_NAME . "</li>";
    echo "</ul>";
}
?>
