<?php
// Se incluye la clase para validar los datos de entrada.
require_once ('../../helpers/validator.php');
// Se incluye la clase padre.
require_once ('../../models/handler/pedidos_handler.php');
/*
 *	Clase para manejar el encapsulamiento de los datos de las tablas PEDIDO y DETALLE_PEDIDO.
 */
class PedidoData extends PedidoHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
     *   Métodos para validar y establecer los datos.
     */
    public function setIdPedido($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_pedido = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del pedido es incorrecto';
            return false;
        }
    }

    public function setIdDetalle($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_detalle_pedido = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del detalle pedido es incorrecto';
            return false;
        }
    }

    public function setIdCliente($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_cliente = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del cliente es incorrecto';
            return false;
        }
    }

    public function setProducto($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->producto = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del producto es incorrecto';
            return false;
        }
    }

    // constructores para el manejo de los datos de la tabla detalles_pedidos.

    public function setCantidadPedido($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->cantidad_pedido = $value;
            return true;
        } else {
            $this->data_error = 'La cantidad del pedido es incorrecta';
            return false;
        }
    }

    public function setIdMueble($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_mueble = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del mueble es incorrecto';
            return false;
        }
    }

    public function setFechaEntrega($value)
    {
        if (Validator::validateDate($value)) {
            $this->fechaEntrega = $value;
            return true;
        } else {
            $this->data_error = 'La fecha de entrega es incorrecta';
            return false;
        }
    }


    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}