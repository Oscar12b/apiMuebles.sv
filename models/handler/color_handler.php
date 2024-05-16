<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla CATEGORIA.
 */
class ColorHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;


    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    <?php
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_color, nombre_color
                FROM tb_colores
                WHERE nombre_color LIKE ?
                ORDER BY nombre_color';
        $params = array($value $value);
        return Database::getRows($sql, $params);
    }
    ?>
    public function createRow()
    {
        $sql = 'INSERT INTO tb_colores(nombre_color)
                VALUES(?)';
        $params = array($this->nombre);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_color, nombre_color
                FROM tb_colores
                ORDER BY nombre_color';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_color, nombre_color
                FROM tb_colores
                WHERE id_color = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }


    public function updateRow()
    {
        $sql = 'UPDATE tb_colores
                SET nombre_color = ?
                WHERE id_color = ?';
        $params = array($this->nombre $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_colores
                WHERE id_color = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}