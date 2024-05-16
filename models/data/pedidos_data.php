<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/pedido_handler.php');

class PedidoData extends PedidoHandler
{
    private $data_error = null;

    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'El id del pedido es incorrecto';
            return false;
        }
    }

    public function getDataError()
    {
        return $this->data_error;
    }
}
?>
