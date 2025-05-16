<?php
// ======= Include Composer Autoload and Load Database Connection =======
require_once '../../vendor/autoload.php'; // Include Composer autoload
require_once '../../config/database.php'; // Database connection

use Dompdf\Dompdf;
use Dompdf\Options;

// ======= Retrieve Required Data from URL =======
$orderId = $_GET['order_id'] ?? null;
$eventIdForJoin = $_GET['event_id'] ?? null;

if (!$orderId || !$eventIdForJoin) {
    die("Insufficient data to generate the PDF.");
}

// ======= Query Order and User Details =======
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
    die("Order not found.");
}

// ======= Query Tickets (Seats) for the Order =======
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

// ======= Configure Dompdf Settings =======
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Allow loading external images
$dompdf = new Dompdf($options);

// ======= Capture HTML Content for the PDF =======
ob_start();
include '../../includes/receipt_pdf_content.php'; // Include receipt content
$html = ob_get_clean();

// ======= Load CSS Styles for the PDF =======
$css = file_get_contents('../../public/css/receipt_pdf.css'); // Path to CSS file
$dompdf->loadHtml('<style>' . $css . '</style>' . $html);

// ======= Set Paper Size and Orientation =======
$dompdf->setPaper('A4', 'portrait');

// ======= Render the PDF =======
$dompdf->render();

// ======= Stream the PDF to the Browser for Download =======
$dompdf->stream('Receipt.pdf', ['Attachment' => true]);