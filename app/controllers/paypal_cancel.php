<?php
require_once '../../config/env.php';
require_once '../../config/database.php';

$orderId = $_GET['order_id'] ?? null;
$eventId = $_GET['event_id'] ?? null;
$paypalOrderId = $_GET['token'] ?? null; // PayPal envía el token (o id) en la URL

if (!$orderId || !$paypalOrderId) {
    die("Datos inválidos.");
}

try {
    // Actualizar el estado de la orden en la base de datos a "cancelled"
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'cancelled' WHERE id = :orderId");
    $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
    $stmt->execute();

    // Redirigir a una página de cancelación para notificar al usuario
    $baseUrl = env('APP_URL');
    header("Location: $baseUrl/public/paypal_cancelled.php?order_id=$orderId&event_id=$eventId");
    exit;
} catch (Exception $e) {
    echo "Error al procesar la cancelación del pago: " . $e->getMessage();
}