<?php
function strClean($cadena)
{
  $string = preg_replace(['/\s+/', '/^\s|\s$/'], [' ', ''], $cadena);
  $string = trim($string);
  $string = stripslashes($string);
  $string = str_ireplace('<script>', '', $string);
  $string = str_ireplace('</script>', '', $string);
  $string = str_ireplace('<script type=>', '', $string);
  $string = str_ireplace('<script src>', '', $string);
  $string = str_ireplace('SELECT * FROM', '', $string);
  $string = str_ireplace('DELETE FROM', '', $string);
  $string = str_ireplace('INSERT INTO', '', $string);
  $string = str_ireplace('SELECT COUNT(*) FROM', '', $string);
  $string = str_ireplace('DROP TABLE', '', $string);
  $string = str_ireplace("OR '1'='1", '', $string);
  $string = str_ireplace('OR ´1´=´1', '', $string);
  $string = str_ireplace('IS NULL', '', $string);
  $string = str_ireplace('LIKE "', '', $string);
  $string = str_ireplace("LIKE '", '', $string);
  $string = str_ireplace('LIKE ´', '', $string);
  $string = str_ireplace('OR "a"="a', '', $string);
  $string = str_ireplace("OR 'a'='a", '', $string);
  $string = str_ireplace('OR ´a´=´a', '', $string);
  $string = str_ireplace('--', '', $string);
  $string = str_ireplace('^', '', $string);
  $string = str_ireplace('[', '', $string);
  $string = str_ireplace(']', '', $string);
  $string = str_ireplace('==', '', $string);
  return $string;
}
function verificar($valor)
{
  $indice = array_search($valor, $_SESSION['permisos'], true);
  return is_numeric($indice);
}

function slugify($text, string $divider = '-')
{
  // replace non letter or digits by divider
  $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, $divider);

  // remove duplicate divider
  $text = preg_replace('~-+~', $divider, $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}
