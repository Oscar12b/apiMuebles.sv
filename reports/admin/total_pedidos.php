<?php
// Incluir la clase para generar reportes PDF
require_once '../../helpers/report.php';

try {
    // Datos estáticos para simular pedidos (reemplazar con datos reales según sea necesario)
    $pedidos = [
        ['id_pedido' => 1, 'cliente' => 'Cliente A', 'fecha_inicio' => '2024-07-19', 'fecha_entrega' => '2024-07-25', 'estado' => 'En proceso', 'total' => 150.00],
        ['id_pedido' => 2, 'cliente' => 'Cliente B', 'fecha_inicio' => '2024-07-20', 'fecha_entrega' => '2024-07-26', 'estado' => 'Entregado', 'total' => 200.00],
        ['id_pedido' => 3, 'cliente' => 'Cliente C', 'fecha_inicio' => '2024-07-21', 'fecha_entrega' => '2024-07-27', 'estado' => 'Pendiente', 'total' => 100.00]
        // Puedes agregar más datos de pedidos según sea necesario
    ];

    // Se instancia la clase para crear el reporte PDF.
    $pdf = new Report;

    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Reporte General de Pedidos');

    // Encabezados de la tabla en el reporte PDF
    $pdf->setFillColor(200, 220, 255); // Color RGB: Azul claro
    $pdf->setFont('Arial', 'B', 12);
    $pdf->cell(30, 10, '#', 1, 0, 'C', 1);
    $pdf->cell(40, 10, 'Cliente', 1, 0, 'C', 1);
    $pdf->cell(40, 10, 'Fecha de inicio', 1, 0, 'C', 1);
    $pdf->cell(40, 10, 'Fecha de entrega', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Estado', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Total', 1, 1, 'C', 1);

    // Se establece la fuente para los datos de los pedidos.
    $pdf->setFont('Arial', '', 12);

    // Recorrer los datos de pedidos estáticos y agregar filas al reporte PDF
    foreach ($pedidos as $pedido) {
        $pdf->cell(30, 10, $pedido['id_pedido'], 1, 0, 'C');
        $pdf->cell(40, 10, $pdf->encodeString($pedido['cliente']), 1, 0);
        $pdf->cell(40, 10, $pedido['fecha_inicio'], 1, 0, 'C');
        $pdf->cell(40, 10, $pedido['fecha_entrega'], 1, 0, 'C');
        $pdf->cell(30, 10, $pedido['estado'], 1, 0, 'C');
        $pdf->cell(30, 10, '$' . number_format($pedido['total'], 2), 1, 1, 'R');
    }

    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'reporte_general_pedidos.pdf');

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>