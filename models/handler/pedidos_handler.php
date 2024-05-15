<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla pedidos.
 */
class PedidosHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id_pedido = null;
    protected $estado_pedido = null;
    protected $fecha_pedido = null;
    protected $fecha_entrega = null;
    protected $direccion_pedido = null;
    protected $id_cliente = null;

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    */

    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_pedido, estado_pedido, fecha_pedido, fecha_entrega, direccion_pedido, nombre_cliente
                FROM tb_pedidos
                INNER JOIN cliente USING(id_cliente)
                WHERE estado_pedido LIKE ? OR fecha_pedido LIKE ? OR fecha_entrega LIKE ? OR direccion_pedido LIKE ?
                ORDER BY fecha_pedido';
        $params = array($value, $value, $value, $value);
        return Database::getRows($sql, $params);
    }
}