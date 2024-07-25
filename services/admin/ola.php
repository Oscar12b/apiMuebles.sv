<?php
// Define el ID de sesión que quieres usar
session_id('6ascvpt7fsevqd97p5892r3hs5');

// Inicia la sesión con el ID definido
session_start();

// Imprime todas las variables de sesión para este ID
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

// Muestra el ID de la sesión actual
$session_id = session_id();
echo "El ID de la sesión es: " . $session_id;


$ip = $_SERVER['SERVER_ADDR'];
echo json_encode(array("server_ip" => $ip));
?>