<?php
require_once '../../config/env.php';
require_once '../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? null;
$ticketQuantity = $data['ticketQuantity'] ?? 0;
$totalPrice = $data['totalPrice'] ?? 0;
$eventId = $data['eventId'] ?? null;

if (!$email || $ticketQuantity <= 0 || $totalPrice <= 0 || !$eventId) {
    http_response_code(400);
    echo json_encode(['message' => 'Datos inválidos']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Registrar o buscar al invitado
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $insertUserStmt = $pdo->prepare("INSERT INTO users (name, email, is_guest) VALUES (:name, :email, 1)");
        $guestName = "Guest";
        $insertUserStmt->bindParam(':name', $guestName, PDO::PARAM_STR);
        $insertUserStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $insertUserStmt->execute();
        $userId = $pdo->lastInsertId();
    } else {
        $userId = $user['id'];
    }

    // 2. Crear la orden en la tabla `orders`
    $insertOrderStmt = $pdo->prepare("
        INSERT INTO orders (user_id, total, payment_method, payment_status) 
        VALUES (:userId, :totalPrice, 'paypal', 'pending')
    ");
    $insertOrderStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $insertOrderStmt->bindParam(':totalPrice', $totalPrice, PDO::PARAM_STR);
    $insertOrderStmt->execute();
    $orderId = $pdo->lastInsertId();

    // 3. Registrar los boletos en la tabla `order_items`
    $insertOrderItemStmt = $pdo->prepare("
        INSERT INTO order_items (order_id, seat_id, price) 
        VALUES (:orderId, NULL, :seatPrice)
    ");
    $seatPrice = $totalPrice / $ticketQuantity; // Precio por boleto
    $insertOrderItemStmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
    $insertOrderItemStmt->bindParam(':seatPrice', $seatPrice, PDO::PARAM_STR);
    for ($i = 0; $i < $ticketQuantity; $i++) {
        $insertOrderItemStmt->execute();
    }

    // 4. Actualizar el stock del evento
    $updateEventStmt = $pdo->prepare("
        UPDATE events 
        SET total_seats = total_seats - :ticketQuantity 
        WHERE id = :eventId AND total_seats >= :ticketQuantity
    ");
    $updateEventStmt->bindParam(':ticketQuantity', $ticketQuantity, PDO::PARAM_INT);
    $updateEventStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    $updateEventStmt->execute();

    if ($updateEventStmt->rowCount() === 0) {
        throw new Exception('No hay suficientes boletos disponibles.');
    }

    // 5. Usar cURL para obtener un token de acceso de PayPal
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

    // 6. Crear la orden en PayPal
    $baseUrl = env('APP_URL'); // obtiene la URL base de .env
    $orderPayload = json_encode([
        "intent" => "CAPTURE",
        "purchase_units" => [
            [
                "amount" => [
                    "currency_code" => "USD",
                    "value"         => $totalPrice,
                ],
                "description" => "Compra de $ticketQuantity boletos para el evento $eventId"
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

    $pdo->commit();

    // Retornar los datos al front-end: la URL para redirigir a PayPal y el orderId
    echo json_encode([
        'approvalUrl' => $approvalUrl,
        'orderId'     => $orderId
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}