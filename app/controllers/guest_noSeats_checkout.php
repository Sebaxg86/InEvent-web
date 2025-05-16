<?php
// ======= Load Environment Variables and Database Connection =======
require_once '../../config/env.php';
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

$email = $data['email'] ?? null;
$ticketQuantity = $data['ticketQuantity'] ?? 0;
$totalPrice = $data['totalPrice'] ?? 0;
$eventId = $data['eventId'] ?? null;

// ======= Validate Input Data =======
if (!$email || $ticketQuantity <= 0 || $totalPrice <= 0 || !$eventId) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid data']);
    exit;
}

try {
    // ======= Begin Database Transaction =======
    $pdo->beginTransaction();

    // ======= 1. Register or Locate the Guest User =======
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // ======= Insert Guest User if Not Found =======
        $insertUserStmt = $pdo->prepare("INSERT INTO users (name, email, is_guest) VALUES (:name, :email, 1)");
        $guestName = "Guest";
        $insertUserStmt->bindParam(':name', $guestName, PDO::PARAM_STR);
        $insertUserStmt->bindParam(':email', $email, PDO::PARAM_STR);
        $insertUserStmt->execute();
        $userId = $pdo->lastInsertId();
    } else {
        $userId = $user['id'];
    }

    // ======= 2. Create an Order in the 'orders' Table =======
    $insertOrderStmt = $pdo->prepare("
        INSERT INTO orders (user_id, total, payment_method, payment_status) 
        VALUES (:userId, :totalPrice, 'paypal', 'pending')
    ");
    $insertOrderStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $insertOrderStmt->bindParam(':totalPrice', $totalPrice, PDO::PARAM_STR);
    $insertOrderStmt->execute();
    $orderId = $pdo->lastInsertId();

    // ======= 3. Insert Tickets into the 'order_items' Table =======
    $insertOrderItemStmt = $pdo->prepare("
        INSERT INTO order_items (order_id, seat_id, event_id, price) 
        VALUES (:orderId, NULL, :eventId, :seatPrice)
    ");
    $seatPrice = $totalPrice / $ticketQuantity; // ======= Calculate Price per Ticket =======
    $insertOrderItemStmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
    $insertOrderItemStmt->bindParam(':eventId',  $eventId,   PDO::PARAM_INT);
    $insertOrderItemStmt->bindParam(':seatPrice', $seatPrice, PDO::PARAM_STR);
    for ($i = 0; $i < $ticketQuantity; $i++) {
        $insertOrderItemStmt->execute();
    }

    // ======= 4. Update the Event's Available Tickets =======
    $updateEventStmt = $pdo->prepare("
        UPDATE events 
        SET total_seats = total_seats - :ticketQuantity 
        WHERE id = :eventId AND total_seats >= :ticketQuantity
    ");
    $updateEventStmt->bindParam(':ticketQuantity', $ticketQuantity, PDO::PARAM_INT);
    $updateEventStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    $updateEventStmt->execute();

    if ($updateEventStmt->rowCount() === 0) {
        throw new Exception('Not enough tickets available.');
    }

    // ======= 5. Obtain PayPal Access Token Using cURL =======
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

    // ======= 6. Create the Order in PayPal =======
    $baseUrl = env('APP_URL'); // ======= Get Base URL from .env =======
    $orderPayload = json_encode([
        "intent" => "CAPTURE",
        "purchase_units" => [
            [
                "amount" => [
                    "currency_code" => "USD",
                    "value"         => $totalPrice,
                ],
                "description" => "Purchase of $ticketQuantity tickets for event $eventId"
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

    // ======= Commit the Transaction =======
    $pdo->commit();

    // ======= Return Approval URL and Order ID to the Front-end =======
    echo json_encode([
        'approvalUrl' => $approvalUrl,
        'orderId'     => $orderId
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}