<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class AdministradorHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $correo = null;
    protected $alias = null;
    protected $clave = null;
    protected $telefono = null;
    protected $idCliente = null;
    protected $estadoCliente = null;

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */
    public function checkUser($username, $password)//CHECK[X]
    {
        $sql = 'SELECT id_administrador, alias_administrador, clave_administrador
                FROM tb_administradores
                WHERE alias_administrador = ?';

        $params = array($username);
        $data = Database::getRow($sql, $params);

        if ($data && password_verify($password, $data['clave_administrador'])) {
            $_SESSION['idAdministrador'] = $data['id_administrador'];
            $_SESSION['aliasAdministrador'] = $data['alias_administrador'];
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword($password)//CHECK[X]
    {
        $sql = 'SELECT clave_administrador
                FROM administrador
                WHERE id_administrador = ?';
        $params = array($_SESSION['idAdministrador']);
        $data = Database::getRow($sql, $params);
        // Se verifica si la contraseña coincide con el hash almacenado en la base de datos.
        if (password_verify($password, $data['clave_administrador'])) {
            return true;
        } else {
            return false;
        }
    }

    public function changePassword()
    {
        $sql = 'UPDATE administrador
                SET clave_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->clave, $_SESSION['idadministrador']);
        return Database::executeRow($sql, $params);
    }

    public function readProfile()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, apellido_administrador, coreo_administrador, alias_administrador
                FROM administrador
                WHERE id_administrador = ?';
        $params = array($_SESSION['idAdministrador']);
        return Database::getRow($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE administrador
                SET nombre_administrador = ?, apellido_administrador = ?, coreo_administrador = ?, alias_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->alias, $_SESSION['idAdministrador']);
        return Database::executeRow($sql, $params);
    }

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_administrador, nombre_administrador, apellido_administrador, coreo_administrador, alias_administrador
                FROM tb_administradores
                WHERE nombre_administrador LIKE ? OR coreo_administrador LIKE ?
                ORDER BY nombre_administrador';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()//check[X]
    {
        $sql = 'INSERT INTO tb_administradores(nombre_administrador, apellido_administrador, coreo_administrador, alias_administrador, clave_administrador, telefono_administrador)
                VALUES(?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->alias, $this->clave, $this->telefono);
        return Database::executeRow($sql, $params);
    }

    public function readAll()//check[X]
    {
        $sql = 'SELECT id_administrador,alias_administrador, nombre_administrador, coreo_administrador, telefono_administrador
                FROM tb_administradores
                ORDER BY apellido_administrador;';
        return Database::getRows($sql);
    }

    public function readOne()//check[X]
    {
        $sql = 'SELECT * FROM tb_administradores WHERE id_administrador = ?;';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()//check[X]
    {
        $sql = 'UPDATE tb_administradores
        SET alias_administrador = ?, nombre_administrador = ?, apellido_administrador = ?, coreo_administrador = ?, telefono_administrador = ?
        WHERE id_administrador = ?';
        $params = array($this->alias, $this->nombre, $this->apellido, $this->correo, $this->telefono, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()//check[X]
    {
        $sql = 'DELETE FROM tb_administradores
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    //metodos para rellenar la tabla de clientes papapapapapapapapaappaapap
    public function readAllCliente()
    {
        $sql = 'SELECT id_cliente, nombre_cliente, correo_cliente, telefono_cliente, estado_cliente 
        FROM tb_clientes 
        ORDER BY nombre_cliente;';
        return Database::getRows($sql);
    }

    public function readOneCliente()
    {
        $sql = 'SELECT clave_cliente, nombre_cliente, apellido_cliente, dui_cliente, telefono_cliente, direccion_cliente, estado_cliente, correo_cliente, DATEDIFF(NOW(), fecha_creacion) AS dias_pasados
                FROM tb_clientes
                WHERE id_cliente = ?;';
        $params = array($this->idCliente);
        return Database::getRow($sql, $params);
    }

    public function updateClienteEstado()
    {
        $sql = 'UPDATE tb_clientes
                SET estado_cliente = ?
                WHERE id_cliente = ?;';
        $params = array($this->estadoCliente, $this->idCliente);
        return Database::executeRow($sql, $params);
    }


}
