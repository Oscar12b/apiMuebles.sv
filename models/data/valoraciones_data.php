<?php
// Se incluye la clase para validar los datos de entrada.
require_once ('../../helpers/validator.php');
// Se incluye la clase padre.
require_once ('../../models/handler/valoraciones_handler.php');
/*
 *	Clase para manejar el encapsulamiento de los datos de las tablas PEDIDO y DETALLE_PEDIDO.
 */
class valoracionesData extends valoracionesHandler
{

    private $data_error = null;


    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'EL identificador de la valoración es incorrecto';
            return false;
        }
    }

    public function setIdDetalle($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_detalle = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del detalle pedido es incorrecto';
            return false;
        }
    }

    public function setPuntuacion($value, $min = 1, $max = 5)
    {
        if (!Validator::validateNaturalNumber($value)) {

            $this->data_error = 'El numero debe de ser entero';

        } elseif ($value >= $min && $value <= $max) {
            $this->puntuacion = $value;
            return true;
        } else {
            $this->data_error = 'El numero debe de estar entre 1 y 5';
            return false;
        }
    }

    public function setComentario($value, $min = 2, $max = 250)
    {
        if (!Validator::validateString($value)) {
            $this->data_error = 'El comentario contiene caracteres prohibidos';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->comentario = $value;
            return true;
        } else {
            $this->data_error = 'El comentario debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}
?>