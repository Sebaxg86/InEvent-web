<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

// Verificar que el método sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
    exit;
}

// Obtener los datos enviados desde el cliente
$data = json_decode(file_get_contents('php://input'), true);

$selectedSeats = $data['selectedSeats'] ?? [];
$totalPrice = $data['totalPrice'] ?? 0;
$eventId = $data['eventId'] ?? null;

// Iniciar la sesión y obtener el ID del usuario autenticado
session_start();
$userId = $_SESSION['user']['id'] ?? null;

if (!$userId || empty($selectedSeats) || $totalPrice <= 0 || !$eventId) {
    http_response_code(400);
    echo json_encode(['message' => 'Datos inválidos']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Crear una orden en la tabla `orders`
    $insertOrderStmt = $pdo->prepare("
        INSERT INTO orders (user_id, total, payment_method, payment_status) 
        VALUES (:userId, :totalPrice, 'paypal', 'pending')
    ");
    $insertOrderStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $insertOrderStmt->bindParam(':totalPrice', $totalPrice, PDO::PARAM_STR);
    $insertOrderStmt->execute();
    $orderId = $pdo->lastInsertId();

    // 2. Registrar los asientos seleccionados en la tabla `order_items`
    $insertOrderItemStmt = $pdo->prepare("
        INSERT INTO order_items (order_id, seat_id, price) 
        VALUES (:orderId, :seatId, :seatPrice)
    ");
    $seatPrice = $totalPrice / count($selectedSeats); // Precio por asiento
    $insertOrderItemStmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
    $insertOrderItemStmt->bindParam(':seatPrice', $seatPrice, PDO::PARAM_STR);

    foreach ($selectedSeats as $seatLabel) {
        // Obtener el ID del asiento
        $seatIdStmt = $pdo->prepare("SELECT id FROM seats WHERE seat_label = :seatLabel AND event_id = :eventId AND is_sold = 0");
        $seatIdStmt->bindParam(':seatLabel', $seatLabel, PDO::PARAM_STR);
        $seatIdStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $seatIdStmt->execute();
        $seatId = $seatIdStmt->fetchColumn();

        if (!$seatId) {
            throw new Exception("El asiento {$seatLabel} no está disponible.");
        }

        // Insertar el asiento en `order_items`
        $insertOrderItemStmt->bindParam(':seatId', $seatId, PDO::PARAM_INT);
        $insertOrderItemStmt->execute();

        // Marcar el asiento como vendido en la tabla `seats`
        $updateSeatStmt = $pdo->prepare("UPDATE seats SET is_sold = 1 WHERE id = :seatId");
        $updateSeatStmt->bindParam(':seatId', $seatId, PDO::PARAM_INT);
        $updateSeatStmt->execute();
    }

    $pdo->commit();

    // 3. Obtener token de acceso de PayPal usando cURL
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
        throw new Exception("Error cURL (token): " . curl_error($ch));
    }
    curl_close($ch);
    $tokenData   = json_decode($tokenResponse, true);
    $accessToken = $tokenData['access_token'] ?? null;
    if (!$accessToken) {
        throw new Exception("No se pudo obtener token de PayPal.");
    }

    // 4. Crear la orden en PayPal
    // Usar APP_URL definida en .env para construir URLs de retorno/cancelación
    $baseUrl = env('APP_URL', 'http://localhost/InEvent-web');
    $orderPayload = json_encode([
        "intent" => "CAPTURE",
        "purchase_units" => [
            [
                "amount" => [
                    "currency_code" => "USD",
                    "value"         => $totalPrice,
                ],
                "description" => "Compra de asientos para el evento $eventId"
            ]
        ],
        "application_context" => [
            "return_url" => "$baseUrl/app/controllers/paypal_success.php?order_id=$orderId&event_id=$eventId",
            "cancel_url" => "$baseUrl/app/controllers/paypal_cancel.php?order_id=$orderId"
        ]
    ]);

    $orderUrl = "https://api-m.sandbox.paypal.com/v2/checkout/orders";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $orderUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $orderPayload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $accessToken"
    ]);
    $orderResponse = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception("Error cURL (order creation): " . curl_error($ch));
    }
    curl_close($ch);
    $orderData = json_decode($orderResponse, true);
    
    // Extraer el link de aprobación de PayPal
    $approvalUrl = "";
    if (isset($orderData['links']) && is_array($orderData['links'])) {
        foreach ($orderData['links'] as $link) {
            if ($link['rel'] === 'approve') {
                $approvalUrl = $link['href'];
                break;
            }
        }
    }
    if (empty($approvalUrl)) {
        throw new Exception("No se obtuvo URL de aprobación de PayPal.");
    }

    // Retornar la URL de aprobación y el orderId al front-end
    echo json_encode([
        'approvalUrl' => $approvalUrl,
        'orderId'     => $orderId
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}