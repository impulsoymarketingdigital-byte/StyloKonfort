<?php
class Conexion {
    private $conect;

    public function __construct() {
        $pdo = "mysql:host=" . HOST . ";dbname=" . DB . ";" . CHARSET;
        
        try {
            $this->conect = new PDO($pdo, USER, PASS);
            
            // 1. Manejo estricto de errores
            $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // 2. Apagar emulación (Seguridad extrema contra Inyección SQL)
            $this->conect->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            // 3. Optimización de memoria (Array asociativo limpio)
            $this->conect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // Guardamos el error real en los logs del servidor para que tú lo veas si algo falla
            error_log("Fallo crítico en BD: " . $e->getMessage());
            
            // Le mostramos un mensaje amigable y genérico al cliente
            die("Estamos realizando tareas de mantenimiento. Por favor, regresa en unos minutos.");
        }
    }

    public function conect() {
        return $this->conect;
    }
}
?>