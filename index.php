<?php
// Cargar el archivo de configuración
require 'helpers/config.php';

// Obtener la ruta solicitada
$requestUri = $_SERVER['REQUEST_URI'];

// Redirigir a los archivos correspondientes según la ruta
if (strpos($requestUri, '/services/public/') === 0) {
    $filePath = __DIR__ . $requestUri;
    if (file_exists($filePath)) {
        require $filePath;
    } else {
        echo json_encode(['error' => 'Archivo no encontrado']);
    }
} elseif (strpos($requestUri, '/services/admin/') === 0) {
    $filePath = __DIR__ . $requestUri;
    if (file_exists($filePath)) {
        require $filePath;
    } else {
        echo json_encode(['error' => 'Archivo no encontrado']);
    }
} else {
    echo json_encode(['error' => 'Ruta no disponible']);
}