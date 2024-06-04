<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class ClienteHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $alias = null;
    protected $clave = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $dui = null;
    protected $telefono = null;
    protected $direccion = null;
    protected $correo = null;

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */
    public function checkUser($mail, $password)
    {
        $sql = 'SELECT id_cliente, alias_cliente, clave_cliente, estado_cliente
                FROM tb_clientes
                WHERE correo_cliente = ? OR alias_cliente = ?';
        $params = array($mail, $mail);
        $data = Database::getRow($sql, $params);

        if (password_verify($password, $data['clave_cliente'])) {
            // Se verifica si el cliente no esta dado de baja de lo contrario no se le da paso al sistema
            if ($data['estado_cliente'] == 'Activo') {
                $_SESSION['idCliente'] = $data['id_cliente'];
                $_SESSION['aliasCliente'] = $data['alias_cliente'];
                return true;

            } else {
                return false;
            }

        } else {
            return false;
        }
    }

    public function changePassword()
    {
        $sql = 'UPDATE cliente
                SET clave_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->clave, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE cliente
                SET nombre_cliente = ?, apellido_cliente = ?, correo_cliente = ?, dui_cliente = ?, telefono_cliente = ?, nacimiento_cliente = ?, direccion_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->dui, $this->telefono, $this->nacimiento, $this->direccion, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function changeStatus()
    {
        $sql = 'UPDATE cliente
                SET estado_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }

    /*
     *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, correo_cliente, dui_cliente, telefono_cliente, nacimiento_cliente, direccion_cliente
                FROM cliente
                WHERE apellido_cliente LIKE ? OR nombre_cliente LIKE ? OR correo_cliente LIKE ?
                ORDER BY apellido_cliente';
        $params = array($value, $value, $value);
        return Database::getRows($sql, $params);
    }



    public function createRow()
    {
        $sql = 'INSERT INTO tb_clientes(alias_cliente, clave_cliente, nombre_cliente, apellido_cliente, dui_cliente, telefono_cliente, direccion_cliente, correo_cliente)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?)';
        $params = array($this->alias, $this->clave, $this->nombre, $this->apellido, $this->dui, $this->telefono, $this->direccion, $this->correo);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, correo_cliente, dui_cliente, estado_cliente
                FROM cliente
                ORDER BY apellido_cliente';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, correo_cliente, dui_cliente, telefono_cliente, nacimiento_cliente, direccion_cliente, estado_cliente
                FROM cliente
                WHERE id_cliente = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE cliente
                SET nombre_cliente = ?, apellido_cliente = ?, dui_cliente = ?, estado_cliente = ?, telefono_cliente = ?, nacimiento_cliente = ?, direccion_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->nombre, $this->apellido, $this->dui, $this->estado, $this->telefono, $this->nacimiento, $this->direccion, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM cliente
                WHERE id_cliente = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function checkDuplicate($dui, $email)
    {
        $sql = 'SELECT id_cliente
                FROM tb_clientes
                WHERE dui_cliente = ? OR correo_cliente = ?';
        $params = array($dui, $email);
        return Database::getRow($sql, $params);
    }


    //---------- cuenta historali de pedidos---------------------
    public function readhistory()
    {
        $sql = 'SELECT c.nombre_cliente, c.apellido_cliente, p.fecha_pedido, p.estado_pedido, SUM(d.precio_pedido * d.cantidad_pedido) AS precio_total
        FROM tb_clientes c
        INNER JOIN tb_pedidos p ON c.id_cliente = p.id_cliente
        INNER JOIN tb_detalles_pedidos d ON p.id_pedido = d.id_pedido
        WHERE c.id_cliente = ?
        GROUP BY c.nombre_cliente, c.apellido_cliente, p.fecha_pedido, p.estado_pedido;';
        $params = array($_SESSION['idCliente']);
        return Database::getRows($sql, $params);
    }
}