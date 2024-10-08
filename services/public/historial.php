<?php
// Se incluye la clase del modelo.
require_once('..\..\models\data\pedidos_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $pedido = new PedidoData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'estado' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como cliente, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idCliente'])) {
        $result['session'] = 1;

        switch ($_GET['action']) {

            case 'readhistory':
                if ($result['dataset'] = $pedido->readhistory()) {
                    $result['status'] = 1;
                    $result['message'] = '';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
                ;

            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $pedido->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'readAllDetallePedido':
                $_POST = Validator::validateForm($_POST);
                if (!$pedido->setIdPedido($_POST['idPedido'])) {
                    $result['error'] = $pedido->getDataError();
                } elseif (($result['dataset'] = $pedido->readDetallesPedidos()) && ($result['estado'] = $pedido->readEstadoPedido())) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay detalles de pedidos disponibles';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible';
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
