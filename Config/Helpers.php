<?php

/**
 * Sanitiza un string para uso general (input de formularios).
 * Elimina espacios extra, slashes y caracteres peligrosos.
 * NO sustituye a los prepared statements para SQL.
 * Para salida en HTML siempre usar h() adicionalmente.
 */
function strClean(string $cadena): string
{
    $string = preg_replace('/\s+/', ' ', $cadena);
    $string = trim($string);
    $string = stripslashes($string);
    return $string;
}

/**
 * Escapa una cadena para mostrar de forma segura en HTML.
 * Usar en TODA salida de datos a plantillas HTML.
 */
function h(?string $cadena): string
{
    return htmlspecialchars($cadena ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Verifica si el usuario admin tiene un permiso específico.
 */
function verificar(string $valor): bool
{
    if (empty($_SESSION['permisos']) || !is_array($_SESSION['permisos'])) {
        return false;
    }
    return in_array($valor, $_SESSION['permisos'], true);
}

/**
 * Convierte un texto a slug URL-amigable.
 */
function slugify(string $text, string $divider = '-'): string
{
    // Reemplazar caracteres no alfanuméricos por el divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    // Transliterar a ASCII
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // Eliminar caracteres no permitidos
    $text = preg_replace('~[^-\w]+~', '', $text);

    // Limpiar extremos
    $text = trim($text, $divider);

    // Eliminar dividers duplicados
    $text = preg_replace('~-+~', $divider, $text);

    // Minúsculas
    $text = strtolower($text);

    return empty($text) ? 'n-a' : $text;
}

/**
 * Genera un token aleatorio seguro para verificación de email.
 */
function generarToken(int $bytes = 32): string
{
    return bin2hex(random_bytes($bytes));
}

/**
 * Retorna la imagen de perfil por defecto si no existe una personal.
 */
function imagenPerfil(?string $perfil): string
{
    if ($perfil && file_exists('assets/images/perfil/' . $perfil)) {
        return BASE_URL . 'assets/images/perfil/' . $perfil;
    }
    return BASE_URL . 'assets/images/perfil/default.png';
}
