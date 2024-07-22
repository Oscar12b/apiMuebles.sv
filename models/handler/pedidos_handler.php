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
        $sql = 'SELECT p.id_pedido, dp.cantidad_pedido, c.nombre_cliente, p.fecha_pedido, p.fecha_entrega, p.estado_pedido, dp.precio_pedido 
        FROM tb_pedidos p
        JOIN tb_detalles_pedidos dp ON p.id_pedido = dp.id_pedido
        JOIN tb_clientes c ON p.id_cliente = c.id_cliente
        WHERE c.nombre_cliente LIKE ? OR p.fecha_pedido LIKE ? OR p.fecha_entrega LIKE ? 
        OR p.estado_pedido LIKE ? OR dp.precio_pedido LIKE ?
        ORDER BY c.nombre_cliente';
        $params = array($value, $value, $value, $value, $value);
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

    public function finishOrder()
    {
        $this->estado = 'Finalizado';
        $sql = 'UPDATE tb_pedidos
                SET estado_pedido = ?
                WHERE id_pedido = ?;';
        $params = array($this->estado, $this->id_pedido);
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
                VALUES(?, NOW() + INTERVAL 7 DAY, (SELECT direccion_cliente FROM tb_clientes WHERE id_cliente = ?), ?);';
        $params = array($this->id_cliente, $this->id_cliente, 'en proceso');
        return Database::executeRow($sql, $params);
    }

    public function checkDisponibilidad($id_detalle, $cantidad)
    {
        $sql = 'CALL checkDisponibilidad(?, ?);';
        $params = array($id_detalle, $cantidad);
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
        $sql = 'CALL actualizar_pedido (?,?,? );
        ';
        $params = array($this->cantidad_pedido, $this->id_mueble, $this->id_detalle_pedido);
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

    //-------FIN DE LOS METODOS OCUPADOS PARA EL CARRITO-----------------------


    ///********************************************************************************************** */
    //-------------INICIO DE LOS METODOS PARA EL HISTORIAL DE PEDIDOS-----------------------------------
    public function readhistory()
    {
        $sql = 'SELECT p.id_pedido, c.nombre_cliente, c.apellido_cliente, p.fecha_pedido, p.estado_pedido, d.precio_pedido, d.cantidad_pedido
                FROM tb_clientes c
                INNER JOIN tb_pedidos p ON c.id_cliente = p.id_cliente
                INNER JOIN tb_detalles_pedidos d ON p.id_pedido = d.id_pedido
                WHERE c.id_cliente = ?
                GROUP BY p.id_pedido';
        $params = array($_SESSION['idCliente']);
        return Database::getRows($sql, $params);
    }

    public function readDetallesPedidos()
    {
        $sql = 'SELECT d.id_detalle_pedido ,m.imagen, m.nombre_mueble, c.nombre_color, mt.nombre_material, d.cantidad_pedido, m.precio, d.precio_pedido
        FROM tb_detalles_pedidos d
        JOIN tb_muebles m ON d.id_mueble = m.id_mueble
        JOIN tb_colores c ON m.id_color = c.id_color
        JOIN tb_materiales mt ON m.id_material = mt.id_material
        WHERE d.id_pedido = ?;';
        $params = array($this->id_pedido);
        return Database::getRows($sql, $params);
    }

    public function readEstadoPedido()
    {
        $sql = 'SELECT estado_pedido FROM tb_pedidos WHERE id_pedido = ?;';
        $params = array($this->id_pedido);
        return Database::getRow($sql, $params);
    }

    public function readPedidoEntrega()
    {
        $sql = 'SELECT COUNT(*) AS cantidad_pedidos_entregados, MONTHNAME(fecha_entrega) AS mes_entrega
                FROM  tb_pedidos
                WHERE estado_pedido = "entregado"
                AND YEAR(fecha_entrega) = YEAR(CURDATE())
                GROUP BY MONTH(fecha_entrega), MONTHNAME(fecha_entrega) 
                ORDER BY MONTH(fecha_entrega);';
        return Database::getRows($sql);
    }

    public function readPrecioTotal()
    {
        $sql = 'SELECT SUM(dp.precio_pedido) AS total_precio, MONTHNAME(p.fecha_pedido) AS nombre_mes
                FROM tb_pedidos p
                JOIN  tb_detalles_pedidos dp ON p.id_pedido = dp.id_pedido
                WHERE p.estado_pedido = "entregado" 
                AND YEAR(p.fecha_pedido) = YEAR(CURDATE())
                GROUP BY nombre_mes, MONTH(p.fecha_pedido)
                ORDER BY  MONTH(p.fecha_pedido);';
        return Database::getRows($sql);
    }

    public function readCantidadMuebles()
    {
        $sql = 'SELECT MONTHNAME(p.fecha_pedido) AS nombre_mes, COUNT(dp.id_detalle_pedido) AS cantidad_muebles_vendidos
                FROM tb_pedidos p
                JOIN tb_detalles_pedidos dp ON p.id_pedido = dp.id_pedido
                WHERE p.estado_pedido = "entregado" AND YEAR(p.fecha_pedido) = YEAR(CURDATE())
                GROUP BY nombre_mes
                ORDER BY MONTH(p.fecha_pedido)';
        return Database::getRows($sql);
    }
    /*
     *   Métodos para generar reportes.
     */
    public function productosPedido()
    {
        $sql = 'SELECT mu.nombre_mueble, c.nombre_color, m.nombre_material, cat.nombre_categoria, dp.cantidad_pedido, SUM(dp.cantidad_pedido * mu.precio) as Precio
                FROM tb_detalles_pedidos dp
                JOIN tb_muebles mu ON dp.id_mueble = mu.id_mueble
                JOIN tb_colores c ON mu.id_color = c.id_color
                JOIN tb_materiales m ON mu.id_material = m.id_material
                JOIN tb_categorias cat ON mu.id_categoria = cat.id_categoria
                WHERE dp.id_pedido = ?
                ORDER BY id_pedido';
        $params = array($this->id_pedido);
        return Database::getRows($sql, $params);
    }
    public function topPedido()
    {
        $sql = 'SELECT m.nombre_mueble, SUM(dp.cantidad_pedido) AS total_vendido
                FROM tb_muebles m
                INNER JOIN tb_detalles_pedidos dp ON m.id_mueble = dp.id_mueble
                INNER JOIN tb_pedidos p ON dp.id_pedido = p.id_pedido
                WHERE YEAR(p.fecha_pedido) = YEAR(CURDATE())  
                GROUP BY m.id_mueble, m.nombre_mueble
                ORDER BY total_vendido DESC
                LIMIT 5';
        return Database::getRows($sql);
    }

    /*
     *   Métodos para generar reportes.
     */

    public function readAllDetallePedido()
    {
        $sql = 'SELECT mu.nombre_mueble, c.nombre_color, m.nombre_material, cat.nombre_categoria, dp.cantidad_pedido, mu.precio
        FROM tb_detalles_pedidos dp
        JOIN tb_muebles mu ON dp.id_mueble = mu.id_mueble
        JOIN tb_colores c ON mu.id_color = c.id_color
        JOIN tb_materiales m ON mu.id_material = m.id_material
        JOIN tb_categorias cat ON mu.id_categoria = cat.id_categoria
        WHERE dp.id_pedido = ?;';
        $params = array($this->id_pedido);
        return Database::getRows($sql, $params);
    }


    public function pedidosFinalizados()
    {
        $sql = 'SELECT p.id_pedido, dp.cantidad_pedido, c.nombre_cliente, p.fecha_pedido, p.fecha_entrega, p.estado_pedido, dp.precio_pedido 
        FROM tb_pedidos p
        JOIN tb_detalles_pedidos dp ON p.id_pedido = dp.id_pedido
        JOIN tb_clientes c ON p.id_cliente = c.id_cliente
        WHERE p.estado_pedido = "Finalizado";';
        $params = array();
        return Database::getRows($sql, $params);
    }

    public function pedidosPendientes()
    {
        $sql = 'SELECT p.id_pedido, dp.cantidad_pedido, c.nombre_cliente, p.fecha_pedido, p.fecha_entrega, p.estado_pedido, dp.precio_pedido 
        FROM tb_pedidos p
        JOIN tb_detalles_pedidos dp ON p.id_pedido = dp.id_pedido
        JOIN tb_clientes c ON p.id_cliente = c.id_cliente
        WHERE p.estado_pedido = "Pendiente";';
        $params = array();
        return Database::getRows($sql, $params);
    }
}
?>