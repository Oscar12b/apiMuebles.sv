/**************************************************************************/
/*------------------------PROCEDIMIENTOS ALAMACENADOS--------------------*/
use railway;

INSERT INTO `tb_categorias` (`id_categoria`, `nombre_categoria`) VALUES
(2, 'comedor'),
(1, 'sala');

INSERT INTO `tb_colores` (`id_color`, `nombre_color`) VALUES
(2, 'amarillo'),
(1, 'rojo');

INSERT INTO `tb_materiales` (`id_material`, `nombre_material`) VALUES
(2, 'madera'),
(1, 'melamina');

INSERT INTO `tb_muebles` (`id_mueble`, `nombre_mueble`, `descripcion_mueble`, `precio`, `precio_antiguo`, `estado`, `stock`, `id_categoria`, `id_color`, `id_material`, `id_administrador`, `imagen`) VALUES
(1, 'mueble de sala ', 'es un mueble muy bueno compreloooo', 12.01, 0.00, 'Disponible', 12, 2, 2, 2, 1, '66c6c614eadfd.png'),
(2, 'mueble pero muy', 'esta mucho mejor ganga comprelo no tengo dinero comprelo', 12.01, 0.00, 'Disponible', 52, 2, 1, 2, 1, '66c6c63fa1e28.png'),
(3, 'mueble pero mej', 'este mueble esta mucho mejor que los otros dinerrrooooo', 13.01, 0.00, 'Disponible', 62, 2, 2, 2, 1, '66c6c67817767.png');



DROP PROCEDURE IF EXISTS agregar_detalle_pedido;
DROP PROCEDURE IF EXISTS checkDisponibilidad;
DROP PROCEDURE IF EXISTS eliminar_detalle_pedido;

/**************************************************************************/
/*------------------------PEDIDOS------------------------*/
/*Este procedimiento almace*/

DELIMITER //
CREATE PROCEDURE agregar_detalle_pedido (
    IN p_id_cliente INT,
    IN p_id_mueble INT,
    IN p_cantidad_pedido INT
)
BEGIN
    DECLARE v_id_pedido INT;
    DECLARE v_precio DECIMAL(10,2);
    DECLARE v_cantidad_pedido_actual INT;

    -- Verificar si existe un pedido en proceso para el cliente
    SELECT id_pedido 
    INTO v_id_pedido 
    FROM tb_pedidos 
    WHERE id_cliente = p_id_cliente AND estado_pedido = 'en proceso'
    LIMIT 1;

    -- Si no existe un pedido en proceso, crear uno
    IF v_id_pedido IS NULL THEN
        INSERT INTO tb_pedidos (id_cliente, estado_pedido)
        VALUES (p_id_cliente, 'en proceso');
        
        -- Obtener el id del nuevo pedido creado
        SET v_id_pedido = LAST_INSERT_ID();
    END IF;

    -- Obtener el precio del mueble
    SELECT precio 
    INTO v_precio 
    FROM tb_muebles 
    WHERE id_mueble = p_id_mueble;

    -- Verificar si ya existe un detalle de pedido para el mueble
    IF NOT EXISTS (SELECT 1 FROM tb_detalles_pedidos WHERE id_mueble = p_id_mueble AND id_pedido = v_id_pedido) THEN
        -- Insertar el nuevo detalle de pedido
        INSERT INTO tb_detalles_pedidos (cantidad_pedido, precio_pedido, id_pedido, id_mueble)
        VALUES (p_cantidad_pedido, v_precio * p_cantidad_pedido, v_id_pedido, p_id_mueble);
    ELSE
        -- Obtener la cantidad actual del pedido
        SELECT cantidad_pedido 
        INTO v_cantidad_pedido_actual 
        FROM tb_detalles_pedidos 
        WHERE id_mueble = p_id_mueble AND id_pedido = v_id_pedido;
        
        -- Actualizar el detalle del pedido con la nueva cantidad
        UPDATE tb_detalles_pedidos 
        SET cantidad_pedido = cantidad_pedido + p_cantidad_pedido, 
            precio_pedido = precio_pedido + (v_precio * p_cantidad_pedido) 
        WHERE id_mueble = p_id_mueble AND id_pedido = v_id_pedido;
    END IF;

    -- Actualizar el stock del mueble
    UPDATE tb_muebles
    SET stock = stock - p_cantidad_pedido
    WHERE id_mueble = p_id_mueble;

    -- Verificar si el stock ha llegado a cero
    IF (SELECT stock FROM tb_muebles WHERE id_mueble = p_id_mueble) = 0 THEN
        UPDATE tb_muebles
        SET estado = 'agotado'
        WHERE id_mueble = p_id_mueble;
    END IF;

END //
DELIMITER ;


/*-----------------------------------------------------------------------*/
/*checkpedido basicamente este procedimiento verifica si la cantidad que se va a modificar o ingresar tiene que haber stock para poder 
realizarse ademas de serivir para eliminar un producto por ejemplo*/

DELIMITER //
CREATE PROCEDURE checkDisponibilidad(IN p_id_detalle INT, IN p_cantidad INT)
BEGIN
    DECLARE v_cantidad_detalle INT;

    -- Obtener la cantidad actual del detalle del pedido para el mueble específico
   SELECT cantidad_pedido 
	INTO v_cantidad_detalle 
    FROM tb_detalles_pedidos 
    WHERE id_detalle_pedido = p_id_detalle
    LIMIT 1;

    -- Comparar la cantidad pasada como parámetro con la cantidad del detalle del pedido
    IF p_cantidad > v_cantidad_detalle THEN
		 SELECT 0 as disponibilidad;
    ELSE
        SELECT 1 as disponibilidad;
    END IF;
END //
DELIMITER ;


/*-----------------------------------------------------------------------*/
/*Procedimietno almacenado para eliminar un registro y restaurar el stock del mueble*/

DELIMITER //
CREATE PROCEDURE eliminar_detalle_pedido (
    IN p_id_detalle_pedido INT
)
BEGIN
    DECLARE v_id_mueble INT;
    DECLARE v_cantidad_pedido INT;

    -- Obtener el id_mueble y la cantidad del detalle del pedido a eliminar
    SELECT id_mueble, cantidad_pedido 
    INTO v_id_mueble, v_cantidad_pedido
    FROM tb_detalles_pedidos 
    WHERE id_detalle_pedido = p_id_detalle_pedido;

    -- Verificar si se encontró el detalle del pedido
    IF v_id_mueble IS NOT NULL THEN
        -- Eliminar el detalle del pedido
        DELETE FROM tb_detalles_pedidos 
        WHERE id_detalle_pedido = p_id_detalle_pedido;

        -- Actualizar el stock del mueble
        UPDATE tb_muebles
        SET stock = stock + v_cantidad_pedido
        WHERE id_mueble = v_id_mueble;

        -- Verificar si el stock ha dejado de ser cero
        IF (SELECT stock FROM tb_muebles WHERE id_mueble = v_id_mueble) > 0 THEN
            UPDATE tb_muebles
            SET estado = 'disponible'
            WHERE id_mueble = v_id_mueble AND estado = 'agotado';
        END IF;
    ELSE
        -- Manejo de error si no se encuentra el detalle del pedido
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se encontró el detalle del pedido para eliminar.';
    END IF;
END //
DELIMITER ;

/*-----------------------------------------------------------------------*/
/*Procedimietno almacenado ACTUALIZAR EL PRECIO Y CANTIDAD DE UN DEATLLE SE OCUPA EN EL INPUT DEL MODAL*/

DELIMITER //
CREATE PROCEDURE actualizar_pedido(
    IN cantidad INT,
    IN id_mueble_pedido INT,
    IN id_detalle_pedido INT
)
BEGIN
    DECLARE precio_mueble DECIMAL(10,2);
    
    -- Obtener el precio del mueble
    SELECT precio INTO precio_mueble FROM tb_muebles WHERE id_mueble = id_mueble_pedido;

    -- Actualizar la cantidad y el precio del pedido en tb_detalles_pedidos
    UPDATE tb_detalles_pedidos
    SET cantidad_pedido = cantidad,
        precio_pedido = cantidad * precio_mueble
    WHERE id_detalle_pedido = id_detalle_pedido AND id_mueble = id_mueble_pedido;
END //
DELIMITER ;



/**************************************************************************/
/*------------------------TRIGGER------------------------*/
/**/
DROP TRIGGER IF EXISTS trg_update_stock_after_update;

DELIMITER //

CREATE TRIGGER trg_update_stock_after_update
AFTER UPDATE ON tb_detalles_pedidos
FOR EACH ROW
BEGIN
    DECLARE diff INT;

    -- Calcular la diferencia entre la nueva cantidad y la cantidad antigua
    SET diff = NEW.cantidad_pedido - OLD.cantidad_pedido;

    -- Actualizar el stock en la tabla tb_muebles
    UPDATE tb_muebles
    SET stock = stock - diff
    WHERE id_mueble = NEW.id_mueble;

    -- Verificar si el stock ha llegado a cero y actualizar el estado
    IF (SELECT stock FROM tb_muebles WHERE id_mueble = NEW.id_mueble) = 0 THEN
        UPDATE tb_muebles
        SET estado = 'agotado'
        WHERE id_mueble = NEW.id_mueble;
    ELSE
        UPDATE tb_muebles
        SET estado = 'disponible'
        WHERE id_mueble = NEW.id_mueble;
    END IF;
END //

DELIMITER ;







