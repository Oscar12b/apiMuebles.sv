<?php
// Se incluye la clase del modelo.
require_once ('../../models/data/producto_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $producto = new ProductoHandler;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'dataset2' => null, 'dataset3' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como producto, de lo contrario se finaliza el script con un mensaje de error.
    /*if (isset($_SESSION['idCliente'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un producto ha iniciado sesión.
        switch ($_GET['action']) {

        

            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {*/
    // Se compara la acción a realizar cuando el producto no ha iniciado sesión.
    switch ($_GET['action']) {

        case 'readOne':
            if (!$producto->setId($_POST['idProducto'])) {
                $result['error'] = 'producto incorrecto';
            } elseif ($result['dataset'] = $producto->readOne()) {
                $result['status'] = 1;
            } else {
                $result['error'] = 'producto inexistente';
            }
            break;

        case 'readAll': //check[X]
            if (($result['dataset'] = $producto->readAllTienda()) && ($result['dataset2'] = $producto->readMinMax())) {
                $result['status'] = 1;
                $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
            } else {
                $result['error'] = 'No existen producto registrados';
            }
            break;

        case 'searchRows': //check[X]
            if (!Validator::validateSearch($_POST['buscador'])) {
                $result['error'] = Validator::getSearchError();
            } elseif ($result['dataset'] = $producto->searchRowsTienda()) {
                $result['status'] = 1;
                $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
            } else {
                $result['error'] = 'No hay coincidencias';
            }
            break;

        case 'filterRows'://check[X]
            $filters = [
                'categoriaMueble' => $_POST['categoriaMueble'] ?? null,
                'materialMueble' => $_POST['materialMueble'] ?? null,
                'colorMueble' => $_POST['colorMueble'] ?? null,
                'precioMinimo' => $_POST['precioMinimo'] ?? null,
                'precioMaximo' => $_POST['precioMaximo'] ?? null,
            ];

            if (($result['dataset'] = $producto->filterRows($filters))) {
                $result['status'] = 1;
                $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
            } else {
                $result['error'] = 'No hay coincidencias';
            }
            break;



        default:
            $result['error'] = 'Acción no disponible fuera de la sesión';
        // }
    }
    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print (json_encode($result));
} else {
    print (json_encode('Recurso no disponible'));
}
