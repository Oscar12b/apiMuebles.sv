<?php
// Se incluye la clase del modelo.
require_once ('../../models/data/pedidos_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $pedido = new PedidoData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'error' => null, 'exception' => null, 'dataset' => null);
    // Se verifica si existe una sesión iniciada como cliente para realizar las acciones correspondientes.
    if (isset($_SESSION['idAdministrador'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {
            //Acción para rellenar la tabla principal de pedidos.
            case 'readAllPedido':
                if ($result['dataset'] = $pedido->readAllPedido()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay pedidos disponibles';
                }
                break;
            //Acción para rellenar la tabla principal del modal de pedidos.
            case 'readAllDetallePedido'://check [X]
                if (!$pedido->setIdPedido($_POST['idPedido'])) {
                    $result['error'] = $pedido->getDataError();
                } elseif ($result['dataset'] = $pedido->readAllDetallePedido()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay detalles de pedidos disponibles';
                }
                break;
            // Acción para finalizar el pedido.
            case 'finishOrder'://check [X]
                if (!$pedido->setIdPedido($_POST['id_pedido'])) {
                    $result['error'] = $pedido->getDataError();
                } elseif ($result['dataset'] = $pedido->finishOrder()) {
                    $result['status'] = 1;
                    $result['message'] = 'Pedido finalizado con éxito';
                } else {
                    $result['error'] = 'Ocurrió un problema al finalizar el pedido';
                }
                break;
            // Acción para buscar filas.
            case 'searchRows'://check[X]
                if (!Validator::validateSearch($_POST['buscador'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $pedido->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'checkOrderStatus':
                if ($pedido->setIdPedido($_POST['id_pedido'])) {
                    if ($result = $pedido->checkOrderStatus()) {
                        $result['status'] = 1;
                        $result['dataset'] = $result;
                    } else {
                        $result['error'] = 'No se pudo obtener el estado del pedido';
                    }
                } else {
                    $result['error'] = 'ID de pedido incorrecto';
                }
                break;

            case 'readPedidoEntrega':
                if ($result['dataset'] = $pedido->readPedidoEntrega()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay pedidos disponibles';
                }
                break;
            case 'readPrecioTotal':
                if ($result['dataset'] = $pedido->readPrecioTotal()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay pedidos disponibles';
                }
                break;
            case 'readCantidadMuebles':
                if ($result['dataset'] = $pedido->readCantidadMuebles()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay pedidos disponibles';
                }
                break;
            case 'topPedido':
                if ($result['dataset'] = $pedido->topPedido()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay pedidos disponibles';
                }
                break;
            case 'estimacion':
                if ($result['dataset'] = $pedido->estimacion()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay pedidos disponibles';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando un cliente no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'createDetail':
                $result['error'] = 'Debe iniciar sesión para agregar el producto al carrito';
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
    print (json_encode($result));
} else {
    print (json_encode('Recurso no disponible'));
}