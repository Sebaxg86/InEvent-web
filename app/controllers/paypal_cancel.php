<?php
// ======= Load Environment Variables and Database Connection =======
require_once '../../config/env.php';
require_once '../../config/database.php';

// ======= Retrieve Required Data from URL =======
$orderId      = $_GET['order_id'] ?? null;
$eventId      = $_GET['event_id'] ?? null;
$paypalOrderId= $_GET['token'] ?? null; // ======= PayPal sends the token (or id) in the URL =======

// ======= Validate Required Data =======
if (!$orderId || !$paypalOrderId) {
    die("Invalid data.");
}

try {
    // ======= Update the Order Payment Status to "cancelled" in the Database =======
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'cancelled' WHERE id = :orderId");
    $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
    $stmt->execute();

    // ======= Redirect to the Cancellation Notification Page =======
    $baseUrl = env('APP_URL');
    header("Location: $baseUrl/paypal_cancelled.php?order_id=$orderId&event_id=$eventId");
    exit;
} catch (Exception $e) {
    // ======= Handle Errors =======
    echo "Error processing payment cancellation: " . $e->getMessage();
}