<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once ('../../helpers/report.php');

// Validar y obtener el ID del pedido de manera segura
$idPedido = isset($_GET['idPedido']) ? intval($_GET['idPedido']) : 0;

if ($idPedido <= 0) {
    die('Debe seleccionar un pedido válido');
}

try {
    // Se incluyen las clases para la transferencia y acceso a datos.
    require_once ('../../models/data/pedidos_data.php');

    // Se instancia la clase para acceder a los datos del pedido.
    $pedido = new PedidoData;

    // Intentar establecer el ID del pedido y obtener detalles del pedido
    if (!$pedido->setIdPedido($idPedido)) {
        die('Pedido incorrecto');
    }

    // Leer detalles completos del pedido
    $rowPedido = $pedido->readAllDetallePedido();

    if (!$rowPedido) {
        die('Pedido inexistente');
    }

    // Se instancia la clase para crear el reporte PDF.
    $pdf = new Report;

    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Pedidos #' . $rowPedido['id_pedido']);

    // Obtener productos del pedido
    $dataPedidos = $pedido->productosPedido();

    if ($dataPedidos) {
        // Se establece un color de relleno para los encabezados.
        $pdf->setFillColor(225);
        // Se establece la fuente para los encabezados.
        $pdf->setFont('Arial', 'B', 11);
        // Se imprimen las celdas con los encabezados.
        $pdf->cell(40, 10, 'Mueble', 1, 0, 'C', 1);
        $pdf->cell(30, 10, 'Color', 1, 0, 'C', 1);
        $pdf->cell(30, 10, 'Material', 1, 0, 'C', 1);
        $pdf->cell(30, 10, 'Categoría', 1, 0, 'C', 1);
        $pdf->cell(30, 10, 'Cantidad', 1, 0, 'C', 1);
        $pdf->cell(30, 10, 'Precio', 1, 1, 'C', 1);

        // Se establece la fuente para los datos de los productos.
        $pdf->setFont('Arial', '', 11);

        // Se recorren los registros fila por fila.
        foreach ($dataPedidos as $row) {
            $estado = $row['estado_pedido'] ? 'Finalizado' : 'Pendiente';
            // Se imprimen las celdas con los datos de los productos.
            $pdf->cell(40, 10, $pdf->encodeString($row['nombre_mueble']), 1, 0);
            $pdf->cell(30, 10, $pdf->encodeString($row['color']), 1, 0);
            $pdf->cell(30, 10, $pdf->encodeString($row['material']), 1, 0);
            $pdf->cell(30, 10, $pdf->encodeString($row['categoria']), 1, 0);
            $pdf->cell(30, 10, $row['cantidad'], 1, 0);
            $pdf->cell(30, 10, $row['precio_pedido'], 1, 1);
        }
    } else {
        $pdf->cell(0, 10, $pdf->encodeString('No hay pedidos'), 1, 1);
    }

    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'pedido.pdf');

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>