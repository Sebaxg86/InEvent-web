<?php
require_once '../../config/env.php';
require_once '../../config/database.php';

$orderId = $_GET['order_id'] ?? null;
$paypalOrderId = $_GET['token'] ?? null; // PayPal envía el token (o id) en la URL

if (!$orderId || !$paypalOrderId) {
    die("Datos inválidos.");
}

try {
    // Obtener token de acceso nuevamente
    $clientId = env('PAYPAL_CLIENT_ID');
    $clientSecret = env('PAYPAL_SECRET_KEY');
    $tokenUrl = "https://api-m.sandbox.paypal.com/v1/oauth2/token";
    
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
        throw new Exception("Error cURL (token): " . curl_error($ch));
    }
    curl_close($ch);
    $tokenData = json_decode($tokenResponse, true);
    $accessToken = $tokenData['access_token'] ?? null;
    if (!$accessToken) {
        throw new Exception("No se obtuvo token de PayPal");
    }
    
    // Capturar la orden en PayPal
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
        throw new Exception("Error cURL (capture): " . curl_error($ch));
    }
    curl_close($ch);
    $captureData = json_decode($captureResponse, true);
    
    // Verifica si la captura fue exitosa (revisa el contenido de $captureData según la documentación)
    if (isset($captureData['status']) && $captureData['status'] == 'COMPLETED') {
        // Actualizar estado de la orden en tu DB a "completed"
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'completed' WHERE id = :orderId");
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
        $stmt->execute();

        // Usamos APP_URL para construir la URL de redirección a confirmation.php (ubicado en public)
        $baseUrl = env('APP_URL');
        header("Location: $baseUrl/public/confirmation.php?order_id=$orderId");
        exit;
    } else {
        throw new Exception("La captura del pago falló.");
    }
    
} catch (Exception $e) {
    echo "Error al capturar el pago: " . $e->getMessage();
}