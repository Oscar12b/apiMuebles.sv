<?php
// Se incluye la clase del modelo.
require_once('../../models/data/pedidos_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $pedidos = new PedidoData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como cliente, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idCliente'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {


            case 'createOrder':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$pedidos->setIdCliente($_SESSION['idCliente']) or
                    !$pedidos->setFechaEntrega($_POST['fechaEntrega'])
                ) {
                    $result['error'] = $pedidos->getDataError();
                } elseif ($pedidos->checkPedidoProceso()) {
                    $result['status'] = 0;
                    $result['error'] = 'Hay pedidos en curso';
                } elseif ($pedidos->checkPedidoPendiente()) {
                    $result['error'] = 'Tienes un pedido pendiente';
                } elseif ($pedidos->createOrder()) {
                    $result['status'] = 1;
                    $result['message'] = 'Pedido creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el pedido';
                }
                break;


            case 'addCart':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$pedidos->setIdCliente($_SESSION['idCliente']) or
                    !$pedidos->setIdMueble($_POST['idMueble']) or
                    !$pedidos->setCantidadPedido($_POST['cantidad'])
                ) {
                    $result['error'] = $pedidos->getDataError();
                } else {
                    $disponibilidad = $pedidos->checkEstado();
                    if ($disponibilidad == 0) {
                        $result['message'] = 'No hay existencias para completar el pedido';
                    } elseif ($pedidos->addCart()) {

                        $result['status'] = 1;
                        $result['message'] = 'Producto agregado al carrito';
                        $result['error'] = $_POST['cantidad'];

                    } else {
                        $result['error'] = 'Ocurrió un problema al agregar el producto al carrito';
                    }
                }
                break;


            case 'updateAmountOrder':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$pedidos->setIdDetalle($_POST['idDetalle']) or
                    !$pedidos->setIdMueble($_POST['idMueble']) or
                    !$pedidos->setCantidadPedido($_POST['cantidad'])
                ) {
                    $result['error'] = $pedidos->getDataError();
                } else {
                    // Verificar disponibilidad del stock
                    $disponibilidad = $pedidos->checkDisponibilidad($_POST['idMueble'], $_POST['cantidad']);
                    if ($disponibilidad == 0) {
                        $result['status'] = 2;
                        $result['message'] = 'No hay existencias suficientes' . $disponibilidad . $_POST['cantidad'] . $_POST['idMueble'];
                    } elseif ($pedidos->updateAmountOrder()) {
                        $result['status'] = 1;
                        $result['message'] = 'Cantidad actualizada correctamente' . $_POST['cantidad'] . $_POST['idMueble'] . $_POST['idDetalle'];
                    } else {
                        $result['error'] = 'Ocurrió un problema al actualizar la cantidad' . $_POST['cantidad'] . $_POST['idMueble'] . $_POST['idDetalle'];
                    }

                }
                break;


            case 'deleteOrder':
                $_POST = Validator::validateForm($_POST);
                if (!$pedidos->setIdDetalle($_POST['idDetalle'])) {
                    $result['error'] = $pedidos->getDataError();
                } elseif ($pedidos->deleteDetalle()) {
                    $result['status'] = 1;
                    $result['message'] = 'Producto eliminado del carrito';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el producto del carrito';
                }
                break;

            case 'finishOrder':
                $_POST = Validator::validateForm($_POST);
                if (!$pedidos->setIdPedido($_POST['idPedido'])) {
                    $result['error'] = $pedidos->getDataError();
                } elseif ($pedidos->finalizarOrden()) {
                    $result['status'] = 1;
                    $result['message'] = 'Pedido finalizado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al finalizar el pedido';
                }
                break;


            case 'readAllPedido':
                if (!$pedidos->setIdCliente($_SESSION['idCliente'])) {
                    $result['error'] = $pedidos->getDataError();
                } elseif ($result['dataset'] = $pedidos->readCarrito()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay nada en tu carrito';
                }
                break;

            case 'readhistory':
                if ($result['dataset'] = $pedido->readhistory()) {
                    $result['status'] = 1;
                    $result['message'] = '';
                } else {
                    $result['error'] = 'No hay coincidencias';
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
