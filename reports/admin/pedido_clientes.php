<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once ('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se verifica si existe un valor para el ID del pedido, de lo contrario se muestra un mensaje.
if (isset($_GET['idPedido'])) {
    // Se incluyen las clases para la transferencia y acceso a datos.
    require_once ('../../models/data/pedidos_data.php');

    // Se instancian las entidades correspondientes.
    $pedido = new PedidoData;

    // Se establece el valor del ID del pedido, de lo contrario se muestra un mensaje.
    if ($pedido->setIdPedido($_GET['idPedido'])) {
        // Se verifica si el pedido existe, de lo contrario se muestra un mensaje.
        if ($rowPedido = $pedido->readAllDetallePedido()) {
            // Se inicia el reporte con el encabezado del documento.
            $pdf->startReport('Detalle del pedido #' . $_GET['idPedido']);

            // Se establece un color de relleno para los encabezados.
            $pdf->setFillColor(0, 51, 102); // Color RGB: Azul oscuro
            $pdf->setTextColor(255, 255, 255); // Color RGB: Blanco
            $pdf->setDrawColor(0, 0, 0); // Color RGB: Negro
            $pdf->setLineWidth(.2);
            $pdf->setFont('Arial', 'B', 11);
            // Se imprimen las celdas con los encabezados.
            $pdf->cell(40, 10, 'Mueble', 1, 0, 'C', 1);
            $pdf->cell(25, 10, 'Color', 1, 0, 'C', 1);
            $pdf->cell(40, 10, 'Material', 1, 0, 'C', 1);
            $pdf->cell(30, 10, 'Categoria', 1, 0, 'C', 1);
            $pdf->cell(25, 10, 'Cantidad', 1, 0, 'C', 1);
            $pdf->cell(30, 10, 'Precio', 1, 1, 'C', 1);

            // Se establece la fuente para los datos del pedido.
            $pdf->setFont('Arial', '', 11);
            $pdf->setTextColor(0, 0, 0); // Color RGB: Negro

            // Variable para almacenar el total de la compra.
            $totalCompra = 0;

            // Se recorren los registros fila por fila.
            foreach ($rowPedido as $detalle) {
                $pdf->cell(40, 10, $pdf->encodeString($detalle['nombre_mueble']), 1, 0, 'C');
                $pdf->cell(25, 10, $pdf->encodeString($detalle['nombre_color']), 1, 0, 'C');
                $pdf->cell(40, 10, $pdf->encodeString($detalle['nombre_material']), 1, 0, 'C');
                $pdf->cell(30, 10, $pdf->encodeString($detalle['nombre_categoria']), 1, 0, 'C');
                $pdf->cell(25, 10, $detalle['cantidad_pedido'], 1, 0, 'C');
                $pdf->cell(30, 10, '$' . number_format($detalle['precio'], 2), 1, 1, 'C');

                // Calcular el total para cada item (cantidad * precio).
                $totalCompra += $detalle['cantidad_pedido'] * $detalle['precio'];
            }

            // Agregar una fila vacía para el espacio.
            $pdf->cell(190, 10, '', 0, 1, 'C');
            $pdf->cell(135, 10, '', 0, 0);

            $pdf->setFillColor(0, 51, 102); // Color RGB: Azul oscuro
            $pdf->setTextColor(255, 255, 255); // Color RGB: Blanco
            $pdf->setDrawColor(0, 0, 0); // Color RGB: Negro
            $pdf->setLineWidth(.2);
            $pdf->setFont('Arial', 'B', 11);
            // Agregar una fila para el total de la compra.
            $pdf->cell(25, 10, 'Total (US)', 1, 0, 'C', 1);

            // Se establece la fuente para los datos del pedido.
            $pdf->setFont('Arial', '', 11);
            $pdf->setFillColor(255);
            $pdf->setTextColor(0, 0, 0); // Color RGB: Negro
            $pdf->cell(30, 10, '$' . number_format($totalCompra, 2), 1, 1, 'C', 1);

            // Se llama implícitamente al método footer() y se envía el documento al navegador web.
            $pdf->output('I', 'reporte_pedido_' . $_GET['idPedido'] . '.pdf');
        } else {
            print ('Pedido inexistente');
        }
    } else {
        print ('ID de pedido incorrecto');
    }
} else {
    print ('Debe proporcionar un ID de pedido');
}
?>