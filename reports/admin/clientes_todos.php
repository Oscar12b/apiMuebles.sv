<?php
// Incluir la clase para generar reportes PDF
require_once '../../helpers/report.php';
require_once '../../models/data/clientes_data.php'; // Asegúrate de incluir el modelo Pedido

try {
    // Instanciar el modelo Pedido
    $cliente = new ClienteData();

    // Obtener todos los pedidos usando el método readAllPedido
    $clientes = $cliente->readAllClientes();

    // Verificar si se obtuvieron resultados
    if (!$cliente) {
        throw new Exception('No hay clientes disponibles');
    }

    // Se instancia la clase para crear el reporte PDF.
    $pdf = new Report;

    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Reporte general de clientes');

    // Encabezados de la tabla en el reporte PDF
    $pdf->SetFillColor(0, 51, 102); // Color RGB: Azul oscuro
    $pdf->SetTextColor(255, 255, 255); // Color RGB: Blanco
    $pdf->SetDrawColor(0, 0, 0); // Color RGB: Negro
    $pdf->SetLineWidth(.2);
    $pdf->setFont('Arial', 'B', 11);
    $pdf->cell(10, 10, '#', 1, 0, 'C', 1);
    $pdf->cell(23, 10, 'Nombre', 1, 0, 'C', 1);
    $pdf->cell(23, 10, 'Apellido', 1, 0, 'C', 1);
    $pdf->cell(60, 10, 'Correo', 1, 0, 'C', 1);
    $pdf->cell(25, 10, $pdf->encodeString('Teléfono'), 1, 0, 'C', 1);
    $pdf->cell(25, 10, 'DUI', 1, 0, 'C', 1);
    $pdf->cell(22, 10, 'Estado', 1, 1, 'C', 1);

    // Se establece la fuente para los datos de los pedidos.
    $pdf->setFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0); // Color RGB: Negro

    // Recorrer los datos de pedidos obtenidos y agregar filas al reporte PDF
    foreach ($clientes as $cliente) {
        $pdf->cell(10, 10, $cliente['id_cliente'], 1, 0, 'C');
        $pdf->cell(23, 10, $pdf->encodeString($cliente['nombre_cliente']), 1, 0, 'C');
        $pdf->cell(23, 10, $pdf->encodeString($cliente['apellido_cliente']), 1, 0, 'C');
        $pdf->cell(60, 10, $cliente['correo_cliente'], 1, 0, 'C');
        $pdf->cell(25, 10, $cliente['telefono_cliente'], 1, 0, 'C');
        $pdf->cell(25, 10, $cliente['dui_cliente'], 1, 0, 'C');
        $pdf->cell(22, 10, $pdf->encodeString($cliente['estado_cliente']), 1, 1, 'C');
    }

    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'reporte_general_clientes.pdf');

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>