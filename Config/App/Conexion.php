<?php
class Conexion
{
    private $conect;

    public function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';' . DB_CHARSET;

        try {
            $this->conect = new PDO($dsn, DB_USER, DB_PASS);

            // Manejo estricto de errores
            $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Seguridad extrema contra SQL Injection — deshabilitar emulación
            $this->conect->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            // Fetch como array asociativo por defecto
            $this->conect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Charset para emojis y caracteres especiales
            $this->conect->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

        } catch (PDOException $e) {
            // Loguear el error real (visible en Docker logs / servidor)
            error_log('[StyloKonfort] Fallo crítico en BD: ' . $e->getMessage());

            // Mensaje genérico al usuario — nunca exponer detalles
            http_response_code(503);
            die(json_encode([
                'error' => true,
                'msg'   => 'Estamos realizando tareas de mantenimiento. Por favor, regresa en unos minutos.'
            ]));
        }
    }

    public function conect(): PDO
    {
        return $this->conect;
    }
}