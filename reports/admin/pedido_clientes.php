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
            $pdf->SetFillColor(0, 51, 102); // Color RGB: Azul oscuro
            $pdf->SetTextColor(255, 255, 255); // Color RGB: Blanco
            $pdf->SetDrawColor(0, 0, 0); // Color RGB: Negro
            $pdf->SetLineWidth(.2);
            $pdf->setFont('Arial', 'B', 12);
            // Se imprimen las celdas con los encabezados.
            $pdf->cell(40, 10, 'Mueble', 1, 0, 'C', 1);
            $pdf->cell(25, 10, 'Color', 1, 0, 'C', 1);
            $pdf->cell(40, 10, 'Material', 1, 0, 'C', 1);
            $pdf->cell(30, 10, 'Categoria', 1, 0, 'C', 1);
            $pdf->cell(25, 10, 'Cantidad', 1, 0, 'C', 1);
            $pdf->cell(30, 10, 'Precio', 1, 1, 'C', 1);

            // Se establece la fuente para los datos del pedido.
            $pdf->setFont('Arial', '', 11);
            $pdf->SetTextColor(0, 0, 0); // Color RGB: Blanco
            // Se recorren los registros fila por fila.
            foreach ($rowPedido as $detalle) {
                $pdf->cell(40, 10, $pdf->encodeString($detalle['nombre_mueble']), 1, 0, 'C');
                $pdf->cell(25, 10, $pdf->encodeString($detalle['nombre_color']), 1, 0, 'C');
                $pdf->cell(40, 10, $pdf->encodeString($detalle['nombre_material']), 1, 0, 'C');
                $pdf->cell(30, 10, $pdf->encodeString($detalle['nombre_categoria']), 1, 0, 'C');
                $pdf->cell(25, 10, $detalle['cantidad_pedido'], 1, 0, 'C');
                $pdf->cell(30, 10, '$' . number_format($detalle['precio'], 2), 1, 1, 'C');
            }

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