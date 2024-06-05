<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
require_once ('../../helpers/validator.php');

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
     *  Métodos para realizar las operaciones de búsqueda y cambio de estado.
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT tb_pedidos.id_pedido, tb_pedidos.fecha_pedido, tb_pedidos.fecha_entrega, tb_clientes.nombre_cliente
                FROM tb_pedidos
                INNER JOIN tb_clientes ON tb_pedidos.id_cliente = tb_clientes.id_cliente
                WHERE tb_pedidos.id_pedido LIKE ?';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    public function readAllPedido()
    {
        $sql = 'SELECT p.id_pedido, dp.cantidad_pedido, c.nombre_cliente, p.fecha_pedido, p.fecha_entrega, p.estado_pedido, dp.precio_pedido 
        FROM tb_pedidos p
        JOIN tb_detalles_pedidos dp ON p.id_pedido = dp.id_pedido
        JOIN tb_clientes c ON p.id_cliente = c.id_cliente;';
        $params = array();
        return Database::getRows($sql, $params);
    }

    public function readAllDetallePedido()
    {
        $sql = 'SELECT mu.nombre_mueble, c.nombre_color, m.nombre_material, cat.nombre_categoria, dp.cantidad_pedido, SUM(dp.cantidad_pedido * mu.precio) as Precio
        FROM tb_detalles_pedidos dp
        JOIN tb_muebles mu ON dp.id_mueble = mu.id_mueble
        JOIN tb_colores c ON mu.id_color = c.id_color
        JOIN tb_materiales m ON mu.id_material = m.id_material
        JOIN tb_categorias cat ON mu.id_categoria = cat.id_categoria
        WHERE dp.id_pedido = ?;';
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }

    public function finishOrder()
    {
        $this->estado = 'Finalizado';
        $sql = 'UPDATE tb_pedidos
                SET estado_pedido = ?
                WHERE id_pedido = ?;';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }
}
?>
