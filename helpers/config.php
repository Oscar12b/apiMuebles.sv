<?php
header('Access-Control-Allow-Origin: *');

date_default_timezone_set('America/El_Salvador');

// Obtener valores de las variables de entorno o usar valores por defecto si no están definidas.
$SERVER = getenv('DB_HOST') ?? 'localhost';
$DATABASE = getenv('DB_DATABASE') ?? 'Mueblessv';
$USERNAME = getenv('DB_USER') ?? 'root';
$PASSWORD = getenv('DB_PASS') ?? '';

// Definir constantes con los valores obtenidos.
define('SERVER', $SERVER);
define('DATABASE', $DATABASE);
define('USERNAME', $USERNAME);
define('PASSWORD', $PASSWORD);


/*
define('SERVER', 'localhost');
define('DATABASE', 'Mueblessv');
define('USERNAME', 'muebles');
define('PASSWORD', '1234');
*/
?>