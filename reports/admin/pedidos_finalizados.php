<?php
// Incluir la clase para generar reportes PDF
require_once '../../helpers/report.php';
require_once '../../models/data/pedidos_data.php'; // Asegúrate de incluir el modelo Pedido

try {
    // Instanciar el modelo Pedido
    $pedido = new PedidoData();

    // Obtener todos los pedidos usando el método readAllPedido
    $pedidos = $pedido->pedidosFinalizados();

    // Verificar si se obtuvieron resultados
    if (!$pedidos) {
        throw new Exception('No hay pedidos disponibles');
    }

    // Se instancia la clase para crear el reporte PDF.
    $pdf = new Report;

    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Reporte de pedidos finalizados');

    // Encabezados de la tabla en el reporte PDF
    $pdf->SetFillColor(0, 51, 102); // Color RGB: Azul oscuro
    $pdf->SetTextColor(255, 255, 255); // Color RGB: Blanco
    $pdf->SetDrawColor(0, 0, 0); // Color RGB: Negro
    $pdf->SetLineWidth(.2);
    $pdf->setFont('Arial', 'B', 12);
    $pdf->cell(13, 10, '#', 1, 0, 'C', 1);
    $pdf->cell(35, 10, 'Cliente', 1, 0, 'C', 1);
    $pdf->cell(40, 10, 'Fecha de inicio', 1, 0, 'C', 1);
    $pdf->cell(40, 10, 'Fecha de entrega', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Estado', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Total', 1, 1, 'C', 1);

    // Se establece la fuente para los datos de los pedidos.
    $pdf->setFont('Arial', '', 12);
    $pdf->SetTextColor(0, 0, 0); // Color RGB: Negro

    // Recorrer los datos de pedidos obtenidos y agregar filas al reporte PDF
    foreach ($pedidos as $pedido) {
        $pdf->cell(13, 10, $pedido['id_pedido'], 1, 0, 'C');
        $pdf->cell(35, 10, $pdf->encodeString($pedido['nombre_cliente']), 1, 0, 'C');
        $pdf->cell(40, 10, $pedido['fecha_pedido'], 1, 0, 'C');
        $pdf->cell(40, 10, $pedido['fecha_entrega'], 1, 0, 'C');
        $pdf->cell(30, 10, $pedido['estado_pedido'], 1, 0, 'C');
        $pdf->cell(30, 10, '$' . number_format($pedido['precio_pedido'], 2), 1, 1, 'C');
    }

    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'reporte_pedidos_finalizados.pdf');

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>