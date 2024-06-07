<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla CATEGORIA.
 */
class valoracionesHandler
{

    protected $id = null;

    protected $id_detalle = null;

    protected $puntuacion = null;

    protected $comentario = null;


    public function createValoracion()
    {
        $sql = 'INSERT INTO tb_valoraciones(id_detalle_pedido, valoracion, mensaje)
                VALUES(?, ?, ?)';
        $params = array(
            $this->id_detalle,
            $this->puntuacion,
            $this->comentario
        );
        return Database::executeRow($sql, $params);
    }

    public function checkDuplicate()
    {
        $sql = 'SELECT COUNT(*) as respuesta FROM tb_valoraciones WHERE id_detalle_pedido = ?';
        $params = array($this->id_detalle);
        $data = Database::getRow($sql, $params);

        if ($data) {
            return $data['respuesta'];
        } else {
            return 0;
        }

    }

}
?>