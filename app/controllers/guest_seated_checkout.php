<?php
require_once '../../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Método no permitido']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? null;
$selectedSeats = $data['selectedSeats'] ?? [];
$totalPrice = $data['totalPrice'] ?? 0;
$eventId = $data['eventId'] ?? null;

if (!$email || empty($selectedSeats) || $totalPrice <= 0 || !$eventId) {
    http_response_code(400);
    echo json_encode(['message' => 'Datos inválidos']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Registrar al invitado en la tabla `users`
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

    // 2. Crear una orden en la tabla `orders`
    $insertOrderStmt = $pdo->prepare("
        INSERT INTO orders (user_id, total, payment_method, payment_status) 
        VALUES (:userId, :totalPrice, 'paypal', 'pending')
    ");
    $insertOrderStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $insertOrderStmt->bindParam(':totalPrice', $totalPrice, PDO::PARAM_STR);
    $insertOrderStmt->execute();
    $orderId = $pdo->lastInsertId();

    // 3. Registrar los asientos seleccionados en la tabla `order_items`
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

    echo json_encode(['message' => 'Compra realizada con éxito', 'orderId' => $orderId]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}