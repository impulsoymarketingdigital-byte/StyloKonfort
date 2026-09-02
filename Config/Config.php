<?php
/**
 * STYLOKONFORT — Configuración de la Aplicación
 *
 * NUNCA hardcodear credenciales aquí.
 * Todas las variables sensibles se leen desde variables de entorno.
 * - Desarrollo local: Config/.env
 * - Docker / Dokploy: variables ENV del contenedor
 */

// ─── Cargar .env si existe (desarrollo local) ────────────────────────────────
$_envFile = __DIR__ . '/.env';
if (file_exists($_envFile)) {
    foreach (file($_envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $_line) {
        $_line = trim($_line);
        if ($_line === '' || $_line[0] === '#') continue;
        if (strpos($_line, '=') !== false) {
            [$_k, $_v] = explode('=', $_line, 2);
            $_k = trim($_k);
            $_v = trim($_v, " \t\"'");
            if (!array_key_exists($_k, $_ENV)) {
                putenv("$_k=$_v");
                $_ENV[$_k] = $_v;
            }
        }
    }
    unset($_envFile, $_line, $_k, $_v);
}

// ─── Helper para leer variable de entorno con valor por defecto ──────────────
if (!function_exists('env')) {
    function env(string $key, string $default = ''): string
    {
        $v = getenv($key);
        return ($v !== false && $v !== '') ? $v : $default;
    }
}

// ─── Base de datos ────────────────────────────────────────────────────────────
define('DB_HOST',    env('DB_HOST',    'localhost'));
define('DB_USER',    env('DB_USER',    'root'));
define('DB_PASS',    env('DB_PASS',    ''));
define('DB_NAME',    env('DB_NAME',    'ecomerce'));
define('DB_CHARSET', 'charset=utf8mb4');

// ─── Aplicación ───────────────────────────────────────────────────────────────
define('BASE_URL',  env('APP_URL',       'http://localhost/ecomerce/'));
define('TITLE',     env('APP_NAME',      'Stylo Konfort'));
define('MONEDA',    env('APP_MONEDA',    'COP. '));
define('CURRENCY',  env('APP_CURRENCY',  'COP'));
define('PORPAGINA', (int) env('APP_POR_PAGINA', '12'));
define('MAXPRECIO', (int) env('APP_MAX_PRECIO',  '5000000'));

// ─── Email SMTP ───────────────────────────────────────────────────────────────
define('CORREO',      env('SMTP_FROM', 'ventas@stylokonfort.com'));
define('USER_SMTP',   env('SMTP_USER', ''));
define('PASS_SMTP',   env('SMTP_PASS', ''));
define('PUERTO_SMTP', (int) env('SMTP_PORT', '465'));
define('HOST_SMTP',   env('SMTP_HOST', ''));

// ─── Modo debug ───────────────────────────────────────────────────────────────
define('APP_DEBUG', filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));

if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// ─── Configuración de sesión segura ──────────────────────────────────────────
// (debe ejecutarse ANTES de session_start())
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime',  3600); // 1 hora
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}