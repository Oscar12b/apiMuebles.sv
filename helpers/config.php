<?php
header('Access-Control-Allow-Origin: *');

date_default_timezone_set('America/El_Salvador');

$SERVER = getenv('DB_HOST');
$DATABASE = getenv('DB_DATABASE');
$USERNAME = getenv('DB_USER');
$PASSWORD = getenv('DB_PASS');


define('SERVER', $SERVER || 'localhost');
define('DATABASE', $DATABASE || 'mueblessv');
define('USERNAME', $USERNAME || 'root');
define('PASSWORD', $PASSWORD || '');

?>