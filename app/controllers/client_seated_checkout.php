<?php
// ======= Load Database Connection =======
require_once '../../config/database.php';

// ======= Set JSON Response Header =======
header('Content-Type: application/json');

// ======= Verify that the Request Method is POST =======
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
    exit;
}

// ======= Retrieve Data Sent from the Client =======
$data = json_decode(file_get_contents('php://input'), true);

$selectedSeats = $data['selectedSeats'] ?? [];
$totalPrice = $data['totalPrice'] ?? 0;
$eventId = $data['eventId'] ?? null;

// ======= Start Session and Get Authenticated User ID =======
session_start();
$userId = $_SESSION['user']['id'] ?? null;

// ======= Validate Input Data =======
if (!$userId || empty($selectedSeats) || $totalPrice <= 0 || !$eventId) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid data']);
    exit;
}

try {
    // ======= Begin Database Transaction =======
    $pdo->beginTransaction();

    // ======= 1. Create an Order in the 'orders' Table =======
    $insertOrderStmt = $pdo->prepare("
        INSERT INTO orders (user_id, total, payment_method, payment_status) 
        VALUES (:userId, :totalPrice, 'paypal', 'pending')
    ");
    $insertOrderStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $insertOrderStmt->bindParam(':totalPrice', $totalPrice, PDO::PARAM_STR);
    $insertOrderStmt->execute();
    $orderId = $pdo->lastInsertId();

    // ======= 2. Register the Selected Seats in the 'order_items' Table =======
    $insertOrderItemStmt = $pdo->prepare("
        INSERT INTO order_items (order_id, seat_id, price) 
        VALUES (:orderId, :seatId, :seatPrice)
    ");
    $seatPrice = $totalPrice / count($selectedSeats); // ======= Calculate price per seat =======
    $insertOrderItemStmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
    $insertOrderItemStmt->bindParam(':seatPrice', $seatPrice, PDO::PARAM_STR);

    foreach ($selectedSeats as $seatLabel) {
        // ======= Retrieve the Seat ID for the given label =======
        $seatIdStmt = $pdo->prepare("SELECT id FROM seats WHERE seat_label = :seatLabel AND event_id = :eventId AND is_sold = 0");
        $seatIdStmt->bindParam(':seatLabel', $seatLabel, PDO::PARAM_STR);
        $seatIdStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $seatIdStmt->execute();
        $seatId = $seatIdStmt->fetchColumn();

        if (!$seatId) {
            throw new Exception("Seat {$seatLabel} is not available.");
        }

        // ======= Insert the Seat into the 'order_items' Table =======
        $insertOrderItemStmt->bindParam(':seatId', $seatId, PDO::PARAM_INT);
        $insertOrderItemStmt->execute();

        // ======= Mark the Seat as Sold in the 'seats' Table =======
        $updateSeatStmt = $pdo->prepare("UPDATE seats SET is_sold = 1 WHERE id = :seatId");
        $updateSeatStmt->bindParam(':seatId', $seatId, PDO::PARAM_INT);
        $updateSeatStmt->execute();
    }

    // ======= Commit the Transaction =======
    $pdo->commit();

    // ======= 3. Obtain PayPal Access Token Using cURL =======
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

    // ======= 4. Create the Order in PayPal =======
    // ======= Use APP_URL defined in .env for return/cancel URLs =======
    $baseUrl = env('APP_URL', 'http://localhost/InEvent-web');
    $orderPayload = json_encode([
        "intent" => "CAPTURE",
        "purchase_units" => [
            [
                "amount" => [
                    "currency_code" => "USD",
                    "value"         => $totalPrice,
                ],
                "description" => "Purchase of seats for event $eventId"
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
        throw new Exception("cURL Error (order creation): " . curl_error($ch));
    }
    curl_close($ch);
    $orderData = json_decode($orderResponse, true);

    // ======= Extract the PayPal Approval URL =======
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
        throw new Exception("Unable to obtain PayPal approval URL.");
    }

    // ======= Return the Approval URL and Order ID to the Front-end =======
    echo json_encode([
        'approvalUrl' => $approvalUrl,
        'orderId'     => $orderId
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}