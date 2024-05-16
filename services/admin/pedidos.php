<?php
require_once('../../models/data/pedidos_data.php');

if (isset($_GET['action'])) {
    session_start();
    $pedido = new PedidoData;
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    
    if (isset($_SESSION['idAdministrador'])) {
        switch ($_GET['action']) {
            case 'searchRows':
                break;
            case 'createRow':
                break;
            case 'readAll':
                break;
            case 'readOne':
                break;
            case 'updateRow':
                break;
            case 'deleteRow':
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
        $result['exception'] = Database::getException();
        header('Content-type: application/json; charset=utf-8');
        print(json_encode($result));
    } else {
        print(json_encode('Acceso denegado'));
    }
} else {
    print(json_encode('Recurso no disponible'));
}
?>
