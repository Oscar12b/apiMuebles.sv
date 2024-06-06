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
    protected $estado = null;
    protected $id_cliente = null;
    protected $fechaEntrega = null;
    protected $direccion = null;

    // Atributos para la tabla detalle del pedido.
    protected $id_detalle_pedido = null;

    protected $cantidad_pedido = null;

    protected $id_mueble = null;

    protected $id_pedido = null;

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
        $sql = 'SELECT mu.nombre_mueble, c.nombre_color, m.nombre_material, cat.nombre_categoria, dp.cantidad_pedido, mu.precio 
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

    //----------------------METODOS PARA LA PARTE DE CLIENTE----------------------

    public function checkPedidoProceso()
    {
        $sql = 'SELECT id_pedido FROM tb_pedidos WHERE id_cliente = ? AND estado_pedido = ?;';
        $params = array($this->id_cliente, 'en proceso');
        return Database::getRow($sql, $params);
    }

    public function checkPedidoPendiente()
    {
        $sql = 'SELECT id_pedido FROM tb_pedidos WHERE id_cliente = ? AND estado_pedido = ?;';
        $params = array($this->id_cliente, 'pendiente');
        return Database::getRow($sql, $params);
    }

    public function createOrder()
    {
        $sql = 'INSERT INTO tb_pedidos(id_cliente, fecha_entrega, direccion_pedido, estado_pedido) 
                VALUES(?, ?, (SELECT direccion_cliente FROM tb_clientes WHERE id_cliente = ?), ?);';
        $params = array($this->id_cliente, $this->fechaEntrega, $this->id_cliente, 'en proceso');
        return Database::executeRow($sql, $params);
    }

    public function checkDisponibilidad()
    {
        $sql = 'CALL checkDisponibilidad(?, ?);';
        $params = array($this->id_detalle_pedido, $this->cantidad_pedido);
        $data = Database::getRow($sql, $params);

        if ($data) {
            return $data['disponibilidad'];
        } else {
            return 0;
        }
    }

    public function checkEstado()
    {
        $sql = 'SELECT estado FROM tb_muebles WHERE id_mueble = ?;';
        $params = array($this->id_mueble);
        $data = Database::getRow($sql, $params);

        if ($data['estado'] == 'disponible') {
            return 1;
        } else {
            return 0;
        }
    }

    public function addCart()
    {
        $sql = 'CALL agregar_detalle_pedido(?, ?, ?);';
        $params = array($this->id_cliente, $this->id_mueble, $this->cantidad_pedido);
        return Database::executeRow($sql, $params);
    }

    public function readCarrito()
    {
        $sql = 'SELECT 
        p.id_pedido AS id_pedido,
        d.id_detalle_pedido AS id_detalle_pedido,
        p.fecha_pedido AS fecha_pedido,
        m.imagen AS imagen_mueble,
        m.id_mueble AS id_mueble,
        m.nombre_mueble AS nombre_mueble,
        c.nombre_color AS color_mueble,
        mt.nombre_material AS material_mueble,
        m.precio AS precio_mueble,
        d.cantidad_pedido AS cantidad_pedido
        FROM 
            tb_muebles m
        JOIN 
            tb_colores c ON m.id_color = c.id_color
        JOIN 
            tb_materiales mt ON m.id_material = mt.id_material
        JOIN 
            tb_detalles_pedidos d ON m.id_mueble = d.id_mueble
        JOIN 
            tb_pedidos p ON d.id_pedido = p.id_pedido
        WHERE 
        p.id_cliente = ? AND p.estado_pedido = ?';

        $params = array($this->id_cliente, 'en proceso');
        return Database::getRows($sql, $params);
    }

    //----------fUNCION PARA CABIAR LA CANTIDAD DE UN DETALLE PEDIDO DE UN MMUEBLE-------------------

    public function updateAmountOrder()
    {
        $sql = 'UPDATE tb_detalles_pedidos
                SET cantidad_pedido = ?
                WHERE id_detalle_pedido = ? AND id_mueble = ?;';
        $params = array($this->cantidad_pedido, $this->id_detalle_pedido, $this->id_mueble);
        return Database::executeRow($sql, $params);
    }

    //----------fUNCION PARA ELIMINAR UN DETALLE PEDIDO DE UN MMUEBLE-------------------

    public function deleteDetalle()
    {
        $sql = 'CALL eliminar_detalle_pedido (?)';
        $params = array($this->id_detalle_pedido);
        return Database::executeRow($sql, $params);
    }

    //-----------FUNCION PARA FINALIZAR PEDIDO-----------------------------------------
    public function finalizarOrden()
    {
        $sql = 'UPDATE tb_pedidos
                SET estado_pedido = ?
                WHERE id_pedido = ?;';
        $params = array('pendiente', $this->id_pedido);
        return Database::executeRow($sql, $params);
    }

}
?>