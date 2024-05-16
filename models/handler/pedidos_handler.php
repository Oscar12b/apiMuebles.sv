<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla pedidos.
 */
class PedidoHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $estado = null;
    protected $fechaEntrega = null;
    protected $direccion = null;

    /*
     *  Métodos para cambiar el estado de un pedido.
     */
    public function cambiarEstado()
    {
        $sql = 'UPDATE tb_pedidos
                SET estado_pedido = ?
                WHERE id_pedido = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }

    /*
     *  Métodos para eliminar un pedido.
     */
    public function eliminarPedido()
    {
        $sql = 'DELETE FROM tb_pedidos
                WHERE id_pedido = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    /*
     *  Métodos para buscar detalles de un pedido.
     */
    public function buscarDetallesPedido()
    {
        $sql = 'SELECT tb_detalles_pedidos.id_detalle_pedido, tb_detalles_pedidos.cantidad_pedido, tb_detalles_pedidos.precio_pedido, tb_muebles.nombre_mueble
                FROM tb_detalles_pedidos
                INNER JOIN tb_muebles ON tb_detalles_pedidos.id_mueble = tb_muebles.id_mueble
                WHERE tb_detalles_pedidos.id_pedido = ?';
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }

    /*
     *  Métodos para realizar las operaciones de búsqueda y cambio de estado.
     */
    public function buscarPedidos()
    {
        $sql = 'SELECT id_pedido, estado_pedido, fecha_pedido, fecha_entrega, direccion_pedido
                FROM tb_pedidos';
        return Database::getRows($sql);
    }

    public function cambiarEstadoPedido()
    {
        $sql = 'UPDATE tb_pedidos
                SET estado_pedido = ?
                WHERE id_pedido = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }
}
?>
