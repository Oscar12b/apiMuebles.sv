<?php
// Se incluye la clase del modelo.
require_once ('../../models/data/valoraciones_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $valoraciones = new valoracionesData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como cliente, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idCliente'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {

            case 'createValoracion':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$valoraciones->setIdDetalle($_POST['idDetalle']) or
                    !$valoraciones->setPuntuacion($_POST['puntaje']) or
                    !$valoraciones->setComentario($_POST['comentario'])
                ) {
                    $result['error'] = $valoraciones->getDataError();
                } elseif ($valoraciones->checkDuplicate() == 1) {
                    $result['status'] = 2;
                    $result['message'] = 'Ya has valorado este producto';
                } elseif ($valoraciones->createValoracion()) {
                    $result['status'] = 1;
                    $result['message'] = 'se envio la valoracion correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear la valoración';
                }
                break;


            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando el cliente no ha iniciado sesión.
        switch ($_GET['action']) {

            default:
                $result['error'] = 'Acción no disponible fuera de la sesión';
        }
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
