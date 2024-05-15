<?php
// Se incluye la clase del modelo.
require_once('..\..\models\data\cliente_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $cliente = new ClienteData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como cliente, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idcliente'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $cliente->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $cliente->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen cliente registrados';
                }
                break;
            case 'readOne':
                if (!$cliente->setId($_POST['idcliente'])) {
                    $result['error'] = 'cliente incorrecto';
                } elseif ($result['dataset'] = $cliente->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'cliente inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setId($_POST['idcliente']) or
                    !$cliente->setNombre($_POST['nombrecliente']) or
                    !$cliente->setApellido($_POST['apellidocliente']) or
                    !$cliente->setCorreo($_POST['correocliente'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'cliente modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el cliente';
                }
                break;
            case 'deleteRow':
                if ($_POST['idcliente'] == $_SESSION['idcliente']) {
                    $result['error'] = 'No se puede eliminar a sí mismo';
                } elseif (!$cliente->setId($_POST['idcliente'])) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'cliente eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el cliente';
                }
                break;
            case 'getUser':
                if (isset($_SESSION['aliascliente'])) {
                    $result['status'] = 1;
                    $result['username'] = $_SESSION['aliascliente'];
                } else {
                    $result['error'] = 'Alias de cliente indefinido';
                }
                break;
            case 'logOut':
                if (session_destroy()) {
                    $result['status'] = 1;
                    $result['message'] = 'Sesión eliminada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cerrar la sesión';
                }
                break;
            case 'readProfile':
                if ($result['dataset'] = $cliente->readProfile()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Ocurrió un problema al leer el perfil';
                }
                break;
            case 'editProfile':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setNombre($_POST['nombrecliente']) or
                    !$cliente->setApellido($_POST['apellidocliente']) or
                    !$cliente->setCorreo($_POST['correocliente']) or
                    !$cliente->setAlias($_POST['aliascliente'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->editProfile()) {
                    $result['status'] = 1;
                    $result['message'] = 'Perfil modificado correctamente';
                    $_SESSION['aliascliente'] = $_POST['aliascliente'];
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el perfil';
                }
                break;
            case 'changePassword':
                $_POST = Validator::validateForm($_POST);
                if (!$cliente->checkPassword($_POST['claveActual'])) {
                    $result['error'] = 'Contraseña actual incorrecta';
                } elseif ($_POST['claveNueva'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Confirmación de contraseña diferente';
                } elseif (!$cliente->setClave($_POST['claveNueva'])) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->changePassword()) {
                    $result['status'] = 1;
                    $result['message'] = 'Contraseña cambiada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cambiar la contraseña';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando el cliente no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readUsers':
                if ($cliente->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                } else {
                    $result['error'] = 'Debe crear un cliente para comenzar';
                }
                break;
            case 'signUp':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setNombre($_POST['nombrecliente']) or
                    !$cliente->setApellido($_POST['apellidocliente']) or
                    !$cliente->setCorreo($_POST['correocliente']) or
                    !$cliente->setAlias($_POST['aliascliente']) or
                    !$cliente->setClave($_POST['clavecliente'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($_POST['clavecliente'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($cliente->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'cliente registrado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al registrar el cliente';
                }
                break;
            case 'logIn':
                $_POST = Validator::validateForm($_POST);
                
                if ($cliente->checkUser($_POST['alias'], $_POST['clave'])) {
                    $result['status'] = 1;
                    $result['message'] = 'Autenticación correcta';
                } else {
                    $result['error'] = 'Credenciales incorrectas';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible fuera de la sesión';
        }
    }
    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
