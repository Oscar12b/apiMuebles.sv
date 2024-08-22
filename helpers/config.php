<?php
header('Access-Control-Allow-Origin: *');

date_default_timezone_set('America/El_Salvador');

// Obtener valores de las variables de entorno o usar valores por defecto si no están definidas.
$SERVER = getenv('DB_HOST') ?? '';
$DATABASE = getenv('DB_DATABASE') ?? '';
$USERNAME = getenv('DB_USER') ?? '';
$PASSWORD = getenv('DB_PASS') ?? '';

// Definir constantes con los valores obtenidos.
define('SERVER', $SERVER);
define('DATABASE', $DATABASE);
define('USERNAME', $USERNAME);
define('PASSWORD', $PASSWORD);

?>