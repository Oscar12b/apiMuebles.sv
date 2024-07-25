<?php
// Incluir la clase para generar reportes PDF
require_once '../../helpers/report.php';
require_once '../../models/data/producto_data.php'; // Asegúrate de incluir el modelo Pedido

try {
    // Instanciar el modelo Pedido
    $producto = new ProductoData();

    // Obtener todos los pedidos usando el método readAllPedido
    $productos = $producto->readAllMuebles();

    // Verificar si se obtuvieron resultados
    if (!$producto) {
        throw new Exception('No hay productos disponibles');
    }

    // Se instancia la clase para crear el reporte PDF.
    $pdf = new Report;

    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Reporte general de productos');

    // Encabezados de la tabla en el reporte PDF
    $pdf->setFillColor(0, 51, 102); // Color RGB: Azul oscuro
    $pdf->setTextColor(255, 255, 255); // Color RGB: Blanco
    $pdf->setDrawColor(0, 0, 0); // Color RGB: Negro
    $pdf->setLineWidth(.2);
    $pdf->setFont('Arial', 'B', 12);
    $pdf->cell(13, 10, '#', 1, 0, 'C', 1);
    $pdf->cell(35, 10, 'Nombre', 1, 0, 'C', 1);
    $pdf->cell(25, 10, 'Precio', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Estado', 1, 0, 'C', 1);
    $pdf->cell(20, 10, 'Stock', 1, 0, 'C', 1);
    $pdf->cell(30, 10, $pdf->encodeString('Categoría'), 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Material', 1, 1, 'C', 1);

    // Se establece la fuente para los datos de los pedidos.
    $pdf->setFont('Arial', '', 12);
    $pdf->setTextColor(0, 0, 0); // Color RGB: Negro

    // Recorrer los datos de pedidos obtenidos y agregar filas al reporte PDF
    foreach ($productos as $producto) {
        $pdf->cell(13, 10, $producto['id_mueble'], 1, 0, 'C');
        $pdf->cell(35, 10, $pdf->encodeString($producto['nombre_mueble']), 1, 0, 'C');
        $pdf->cell(25, 10, '$' . number_format($producto['precio'], 2), 1, 0, 'C');
        $pdf->cell(30, 10, $pdf->encodeString($producto['estado']), 1, 0, 'C');
        $pdf->cell(20, 10, $producto['stock'], 1, 0, 'C');
        $pdf->cell(30, 10, $pdf->encodeString($producto['nombre_categoria']), 1, 0, 'C');
        $pdf->cell(30, 10, $pdf->encodeString($producto['nombre_material']), 1, 1, 'C');
    }

    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'reporte_general_producto.pdf');

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>