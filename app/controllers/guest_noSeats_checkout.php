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

    // 1. Registrar al invitado en la tabla `users`
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Crear un nuevo usuario invitado
        $insertUserStmt = $pdo->prepare("INSERT INTO users (name, email, is_guest) VALUES (:name, :email, 1)");
        $guestName = "Guest"; // Nombre predeterminado para usuarios invitados
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

    $pdo->commit();

    echo json_encode(['message' => 'Compra realizada con éxito', 'orderId' => $orderId]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}
