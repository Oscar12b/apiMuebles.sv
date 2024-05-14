<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de la tabla PRODUCTO.
*/


class ProductoHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id = null;
    protected $nombre = null;
    protected $descripcion = null;
    protected $precio = null;
    protected $precio_antiguo = null;
    protected $estado = null; 
    protected $stock = null;
    protected $id_categoria = null;
    protected $id_color = null;
    protected $id_material = null;
    protected $id_administrador = null;  
    protected $imagen = null; 

    // Constante para establecer la ruta de las imágenes.
    const RUTA_IMAGEN = '../../imagenes/productos';

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_producto, imagen_producto, nombre_producto, descripcion_producto, precio_producto, nombre_categoria, estado_producto
                FROM producto
                INNER JOIN categoria USING(id_categoria)
                WHERE nombre_producto LIKE ? OR descripcion_producto LIKE ?
                ORDER BY nombre_producto';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tb_muebles (imagen, nombre_mueble, descripcion_mueble, precio, stock, id_categoria, id_color, id_material, id_administrador) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);';
        $params = array($this-> imagen, $this->nombre, $this->descripcion, $this->precio, $this->stock, $this->id_categoria, $this->id_color, $this->id_material, $_SESSION['idAdministrador']);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_mueble,imagen,nombre_mueble,precio,estado,stock,nombre_categoria,nombre_material 
                FROM tb_muebles 
                INNER JOIN tb_categorias 
                ON tb_muebles.id_categoria = tb_categorias.id_categoria 
                INNER JOIN tb_materiales 
                ON tb_muebles.id_material = tb_materiales.id_material;';

        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_mueble as idMueble, imagen as imagenMueble, nombre_mueble as nombreMueble, descripcion_mueble as descripcionMueble, precio as precioMueble, stock as stockMueble, id_categoria as categoriaMueble, id_color as colorMueble, id_material as materialMueble 
                FROM tb_muebles
                WHERE id_mueble = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function readFilename()
    {
        $sql = 'SELECT imagen
                FROM tb_muebles
                WHERE id_mueble = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_muebles
            SET imagen = ?, nombre_mueble = ?, descripcion_mueble = ?, precio = ?, stock = ?, id_categoria = ?, id_color = ? ,id_material = ?
            WHERE id_mueble = ?';
        $params = array($this-> imagen, $this->nombre, $this->descripcion, $this->precio, $this->stock, $this->id_categoria, $this->id_color, $this->id_material, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_muebles
                WHERE id_mueble = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function readProductosCategoria()
    {
        $sql = 'SELECT id_producto, imagen_producto, nombre_producto, descripcion_producto, precio_producto, existencias_producto
                FROM producto
                INNER JOIN categoria USING(id_categoria)
                WHERE id_categoria = ? AND estado_producto = true
                ORDER BY nombre_producto';
        $params = array($this->categoria);
        return Database::getRows($sql, $params);
    }

    /*
    *   Métodos para generar gráficos.
    */
    public function cantidadProductosCategoria()
    {
        $sql = 'SELECT nombre_categoria, COUNT(id_producto) cantidad
                FROM producto
                INNER JOIN categoria USING(id_categoria)
                GROUP BY nombre_categoria ORDER BY cantidad DESC LIMIT 5';
        return Database::getRows($sql);
    }

    public function porcentajeProductosCategoria()
    {
        $sql = 'SELECT nombre_categoria, ROUND((COUNT(id_producto) * 100.0 / (SELECT COUNT(id_producto) FROM producto)), 2) porcentaje
                FROM producto
                INNER JOIN categoria USING(id_categoria)
                GROUP BY nombre_categoria ORDER BY porcentaje DESC';
        return Database::getRows($sql);
    }

    /*
    *   Métodos para generar reportes.
    */
    public function productosCategoria()
    {
        $sql = 'SELECT nombre_producto, precio_producto, estado_producto
                FROM producto
                INNER JOIN categoria USING(id_categoria)
                WHERE id_categoria = ?
                ORDER BY nombre_producto';
        $params = array($this->categoria);
        return Database::getRows($sql, $params);
    }
}