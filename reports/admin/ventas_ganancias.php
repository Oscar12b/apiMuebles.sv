<?php
// Incluir la clase para generar reportes PDF
require_once '../../helpers/report.php';
require_once '../../models/data/producto_data.php'; // Asegúrate de incluir el modelo ProductoData

try {
    // Instanciar el modelo ProductoData
    $productoData = new ProductoData();

    // Obtener todos los datos de las ventas usando el método obtenerDatosVentas
    $datosVentas = $productoData->obtenerDatosVentas();

    // Verificar si se obtuvieron resultados
    if (!$datosVentas) {
        throw new Exception('No hay datos disponibles para generar el reporte');
    }

    // Instanciar la clase para crear el reporte PDF
    $pdf = new Report;

    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Reporte de ventas y ganancias');

    // Encabezados de la tabla en el reporte PDF
    $pdf->setFillColor(0, 51, 102); // Color RGB: Azul oscuro
    $pdf->setTextColor(255, 255, 255); // Color RGB: Blanco
    $pdf->setDrawColor(0, 0, 0); // Color RGB: Negro
    $pdf->setLineWidth(.2);
    $pdf->setFont('Arial', 'B', 12);
    $pdf->cell(15, 10, '#', 1, 0, 'C', 1);
    $pdf->cell(45, 10, 'Nombre', 1, 0, 'C', 1);
    $pdf->cell(45, 10, $pdf->encodeString('Categoría'), 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Ventas', 1, 0, 'C', 1);
    $pdf->cell(45, 10, 'Ganancias', 1, 1, 'C', 1);

    // Establecer la fuente para los datos de los muebles
    $pdf->setFont('Arial', '', 12);
    $pdf->setTextColor(0, 0, 0); // Color RGB: Negro

    $totalGanancias = 0;

    // Recorrer los muebles obtenidos
    foreach ($datosVentas as $mueble) {
        $pdf->cell(15, 10, $mueble['id_mueble'], 1, 0, 'C');
        $pdf->cell(45, 10, $pdf->encodeString($mueble['nombre_mueble']), 1, 0, 'C');
        $pdf->cell(45, 10, $pdf->encodeString($mueble['nombre_categoria']), 1, 0, 'C');
        $pdf->cell(30, 10, $mueble['ventas'], 1, 0, 'C');
        $pdf->cell(45, 10, '$' . number_format($mueble['ganancias'], 2), 1, 1, 'C');

        // Sumar las ganancias del mueble al total
        $totalGanancias += $mueble['ganancias'];
    }

    // Agregar una fila vacía para el espacio.
    $pdf->cell(190, 10, '', 0, 1, 'C');
    $pdf->cell(105, 10, '', 0, 0);

    $pdf->setFillColor(0, 51, 102); // Color RGB: Azul oscuro
    $pdf->setTextColor(255, 255, 255); // Color RGB: Blanco
    $pdf->setDrawColor(0, 0, 0); // Color RGB: Negro
    $pdf->setLineWidth(.2);
    $pdf->setFont('Arial', 'B', 12);
    // Agregar una fila para el total de la compra.
    $pdf->cell(30, 10, 'Total (US)', 1, 0, 'C', 1);

    // Se establece la fuente para los datos del pedido.
    $pdf->setFont('Arial', '', 12);
    $pdf->setFillColor(255);
    $pdf->setTextColor(0, 0, 0); // Color RGB: Negro
    $pdf->cell(45, 10, '$' . number_format($totalGanancias, 2), 1, 1, 'C', 1);

    // Enviar el documento al navegador web
    $pdf->output('I', 'reporte_ventas_muebles.pdf');

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>