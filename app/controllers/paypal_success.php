<?php
require_once '../../config/env.php';
require_once '../../config/database.php';

$orderId      = $_GET['order_id'] ?? null;
$eventId      = $_GET['event_id'] ?? null;
$paypalOrderId= $_GET['token'] ?? null; // ======= PayPal sends the token (or id) via the URL =======

if (!$orderId || !$paypalOrderId) {
    die("Invalid data.");
}

try {
    // ======= Obtain a New Access Token from PayPal =======
    $clientId     = env('PAYPAL_CLIENT_ID');
    $clientSecret = env('PAYPAL_SECRET_KEY');
    $tokenUrl     = "https://api-m.sandbox.paypal.com/v1/oauth2/token";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $clientSecret);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: application/json",
        "Accept-Language: en_US"
    ]);
    $tokenResponse = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception("cURL Error (token): " . curl_error($ch));
    }
    curl_close($ch);
    $tokenData   = json_decode($tokenResponse, true);
    $accessToken = $tokenData['access_token'] ?? null;
    if (!$accessToken) {
        throw new Exception("Unable to obtain PayPal token.");
    }
    
    // ======= Capture the Order in PayPal =======
    $captureUrl = "https://api-m.sandbox.paypal.com/v2/checkout/orders/{$paypalOrderId}/capture";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $captureUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $accessToken",
    ]);
    $captureResponse = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception("cURL Error (capture): " . curl_error($ch));
    }
    curl_close($ch);
    $captureData = json_decode($captureResponse, true);
    
    // ======= Verify if the Payment Capture is Successful =======
    if (isset($captureData['status']) && $captureData['status'] == 'COMPLETED') {
        // ======= Update Order Payment Status to "completed" in the Database =======
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'completed' WHERE id = :orderId");
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->execute();

        // ======= Build Redirection URL to Receipt Page Using APP_URL =======
        $baseUrl = env('APP_URL');
        header("Location: $baseUrl/public/receipt_pdf.php?order_id=$orderId&event_id=$eventId");
        exit;
    } else {
        throw new Exception("Payment capture failed.");
    }
    
} catch (Exception $e) {
    // ======= Handle Any Exceptions That Occur =======
    echo "Error capturing payment: " . $e->getMessage();
}