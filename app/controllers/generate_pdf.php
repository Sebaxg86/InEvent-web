<?php
require_once '../../vendor/autoload.php'; // Incluye Composer autoload
require_once '../../config/database.php'; // Conexión a la base de datos

use Dompdf\Dompdf;
use Dompdf\Options;

// Obtener los datos necesarios desde la URL
$orderId = $_GET['order_id'] ?? null;
$eventIdForJoin = $_GET['event_id'] ?? null;

if (!$orderId || !$eventIdForJoin) {
    die("Datos insuficientes para generar el PDF.");
}

// Consulta para obtener los detalles de la orden y el usuario
$stmt = $pdo->prepare("
    SELECT 
        o.id AS order_id,
        o.created_at,
        o.total,
        o.payment_method,
        u.name AS customer_name,
        u.email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.id = :orderId
");
$stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
$stmt->execute();
$orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orderDetails) {
    die("Orden no encontrada.");
}

// Consulta para obtener los tickets (asientos) de la orden
$stmt2 = $pdo->prepare("
    SELECT 
        oi.id AS ticket_id,
        oi.price,
        IFNULL(s.seat_label, '-') AS seat_label,
        e.title AS event_name,
        e.event_date,
        e.venue AS location,
        e.img AS event_image
    FROM order_items oi
    LEFT JOIN seats s ON oi.seat_id = s.id
    LEFT JOIN events e ON e.id = COALESCE(s.event_id, :eventId)
    WHERE oi.order_id = :orderId
");
$stmt2->bindParam(':orderId', $orderId, PDO::PARAM_INT);
$stmt2->bindParam(':eventId', $eventIdForJoin, PDO::PARAM_INT);
$stmt2->execute();
$tickets = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Configurar Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Permite cargar imágenes externas
$dompdf = new Dompdf($options);

// Capturar el contenido HTML
ob_start();
include '../../includes/receipt_pdf_content.php'; // Incluye el contenido del recibo
$html = ob_get_clean();

// Agregar estilos CSS al PDF
$css = file_get_contents('../../public/css/receipt_pdf.css'); // Ruta al archivo CSS
$dompdf->loadHtml('<style>' . $css . '</style>' . $html);

// Configurar el tamaño de la página y la orientación
$dompdf->setPaper('A4', 'portrait');

// Renderizar el PDF
$dompdf->render();

// Enviar el PDF al navegador para descargar
$dompdf->stream('Receipt.pdf', ['Attachment' => true]);