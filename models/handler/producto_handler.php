<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *	Clase para manejar el comportamiento de los datos de la tabla PRODUCTO.
 */


class ProductoHandler
{

    //--- variables providas


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
        $sql = 'SELECT id_mueble,imagen,nombre_mueble,precio,estado,stock,nombre_categoria,nombre_material 
        FROM tb_muebles 
        INNER JOIN tb_categorias 
        ON tb_muebles.id_categoria = tb_categorias.id_categoria 
        INNER JOIN tb_materiales 
        ON tb_muebles.id_material = tb_materiales.id_material
        WHERE nombre_mueble LIKE ? OR nombre_categoria LIKE ? OR precio LIKE ?
        OR stock LIKE ? OR nombre_material LIKE ? ORDER BY nombre_mueble';
        $params = array($value, $value, $value, $value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tb_muebles (imagen, nombre_mueble, descripcion_mueble, precio, stock, id_categoria, id_color, id_material, id_administrador) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);';
        $params = array($this->imagen, $this->nombre, $this->descripcion, $this->precio, $this->stock, $this->id_categoria, $this->id_color, $this->id_material, $_SESSION['idAdministrador']);
        return Database::executeRow($sql, $params);
    }

    public function checkDuplicate()
    {
        $sql = 'SELECT COUNT(*) FROM tb_muebles 
                WHERE id_material = ? AND id_color = ? AND id_categoria = ? AND nombre_mueble = ?';
        $params = array($this->id_categoria, $this->id_color, $this->id_material, $this->nombre);
        return Database::getRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT 
                    m.id_mueble,
                    m.imagen,
                    m.nombre_mueble,
                    m.precio,
                    m.estado,
                    m.stock,
                    c.nombre_categoria,
                    mat.nombre_material,
                    IFNULL(v.promedio_valoracion, 0) AS promedio_valoracion
                FROM 
                    tb_muebles m
                INNER JOIN 
                    tb_categorias c ON m.id_categoria = c.id_categoria
                INNER JOIN 
                    tb_materiales mat ON m.id_material = mat.id_material
                LEFT JOIN (
                    SELECT 
                        dp.id_mueble,
                        ROUND(AVG(v.valoracion), 2) AS promedio_valoracion
                    FROM 
                        tb_valoraciones v
                    INNER JOIN 
                        tb_detalles_pedidos dp ON v.id_detalle_pedido = dp.id_detalle_pedido
                    GROUP BY 
                        dp.id_mueble
                ) v ON m.id_mueble = v.id_mueble;';

        return Database::getRows($sql);
    }

    public function readAllMuebles()
    {
        $sql = 'SELECT id_mueble, nombre_mueble, precio, estado, stock, nombre_categoria, nombre_material 
                FROM tb_muebles 
                INNER JOIN tb_categorias 
                ON tb_muebles.id_categoria = tb_categorias.id_categoria 
                INNER JOIN tb_materiales 
                ON tb_muebles.id_material = tb_materiales.id_material;';

        return Database::getRows($sql);
    }

    public function obtenerDatosVentas()
    {
        $sql = 'SELECT m.id_mueble, m.nombre_mueble, nombre_categoria, COUNT(dp.id_detalle_pedido) AS ventas, SUM(dp.precio_pedido) AS ganancias 
                FROM tb_muebles m
                INNER JOIN tb_detalles_pedidos dp ON m.id_mueble = dp.id_mueble
                INNER JOIN tb_categorias c ON m.id_categoria = c.id_categoria 
                GROUP BY m.id_mueble, m.nombre_mueble';
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

    public function readRow()
    {
        $sql = 'SELECT 
        m.nombre_mueble,
        m.descripcion_mueble,
        m.precio,
        m.imagen,
        m.stock,
        m.estado,
        m.precio_antiguo,
        m.id_mueble,
        (
            SELECT AVG(v.valoracion) 
            FROM tb_valoraciones v
            INNER JOIN tb_detalles_pedidos dp ON v.id_detalle_pedido = dp.id_detalle_pedido
            WHERE dp.id_mueble = m.id_mueble
        ) AS puntaje
        FROM 
            tb_muebles m; WHERE id_mueble = ?';
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
        $params = array($this->imagen, $this->nombre, $this->descripcion, $this->precio, $this->stock, $this->id_categoria, $this->id_color, $this->id_material, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_muebles
                WHERE id_mueble = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }



    //--------------------Métodos para la tabla producto--------------------

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


    ///*********************************************************** */
    //--------------------Metodos para la parte publica--------------------

    public function searchRowsTienda()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_mueble,imagen,nombre_mueble,precio,precio_antiguo,estado,stock,nombre_categoria,nombre_material 
         FROM tb_muebles 
         INNER JOIN tb_categorias 
         ON tb_muebles.id_categoria = tb_categorias.id_categoria 
         INNER JOIN tb_materiales 
         ON tb_muebles.id_material = tb_materiales.id_material
         WHERE nombre_mueble LIKE ? OR nombre_categoria LIKE ? OR precio LIKE ?
         OR stock LIKE ? OR nombre_material LIKE ? ORDER BY nombre_mueble';
        $params = array($value, $value, $value, $value, $value);
        return Database::getRows($sql, $params);
    }

    //funcion para leer el precio maximo y minimo de los productos 
    public function readMinMax()
    {
        $sql = 'SELECT MAX(precio) as maximo, MIN(precio) as minimo
                 FROM tb_muebles';
        return Database::getRow($sql);
    }

    //buscar muebles segun su categoria filtro
    public function filterRows($filters)
    {
        $sql = 'SELECT id_mueble, imagen, nombre_mueble, precio, precio_antiguo, estado, stock, nombre_categoria, nombre_material 
            FROM tb_muebles 
            INNER JOIN tb_categorias ON tb_muebles.id_categoria = tb_categorias.id_categoria 
            INNER JOIN tb_materiales ON tb_muebles.id_material = tb_materiales.id_material 
            WHERE 1=1';

        $params = [];

        if (!empty($filters['categoriaMueble'])) {
            $sql .= ' AND tb_muebles.id_categoria = ?';
            $params[] = $filters['categoriaMueble'];
        }

        if (!empty($filters['materialMueble'])) {
            $sql .= ' AND tb_muebles.id_material = ?';
            $params[] = $filters['materialMueble'];
        }

        if (!empty($filters['colorMueble'])) {
            $sql .= ' AND tb_muebles.id_color = ?';
            $params[] = $filters['colorMueble'];
        }

        if (!empty($filters['precioMinimo'])) {
            $sql .= ' AND precio >= ?';
            $params[] = $filters['precioMinimo'];
        }

        if (!empty($filters['precioMaximo'])) {
            $sql .= ' AND precio <= ?';
            $params[] = $filters['precioMaximo'];
        }

        $sql .= ' ORDER BY nombre_mueble';

        return Database::getRows($sql, $params);
    }

    //leer los productos de la tienda
    public function readAllTienda()
    {
        $sql = 'SELECT id_mueble,imagen,nombre_mueble,precio,precio_antiguo,estado,stock,nombre_categoria,nombre_material 
                 FROM tb_muebles 
                 INNER JOIN tb_categorias 
                 ON tb_muebles.id_categoria = tb_categorias.id_categoria 
                 INNER JOIN tb_materiales 
                 ON tb_muebles.id_material = tb_materiales.id_material;';

        return Database::getRows($sql);
    }



}