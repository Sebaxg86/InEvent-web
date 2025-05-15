<?php
require_once '../config/database.php'; // Conexión a la base de datos

// Obtener el ID del usuario desde la sesión
$userId = $_SESSION['user']['id'];

// Consulta para obtener las compras del usuario con detalles del evento
$stmt = $pdo->prepare("
    SELECT 
        o.id AS order_id,
        o.created_at AS order_date,
        o.total AS order_total,
        o.payment_method,
        oi.price AS ticket_price,
        s.seat_label,
        e.title AS event_title,
        e.event_date,
        e.venue AS event_venue,
        e.img AS event_image
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN seats s ON oi.seat_id = s.id
    LEFT JOIN events e ON s.event_id = e.id
    WHERE o.user_id = :userId
    ORDER BY o.created_at DESC
");
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="welcome-message">
    <h2>Your Information</h2>
</section>

<div class="user-card">
    <div class="card">
        <div class="user-info">
            <h3>Basic Information</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email']) ?></p>

            <!-- Logout button -->
            <form action="../public/logout.php">
                <div class="btn-div">
                    <button class="btn-danger">Logout</button>
                </div>
            </form>
        </div>
        <hr>
        <div class="user-purchases">
            <h3>Purchases</h3>
            <ul>
                <?php if (!empty($purchases)): ?>
                    <?php foreach ($purchases as $purchase): ?>
                        <li>
                            <strong>Order ID:</strong> <?= htmlspecialchars($purchase['order_id']) ?> |
                            <strong>Date:</strong> <?= htmlspecialchars($purchase['order_date']) ?> |
                            <strong>Total:</strong> $<?= htmlspecialchars($purchase['order_total']) ?>
                            <br>
                            <strong>Event:</strong> <?= htmlspecialchars($purchase['event_title']) ?> |
                            <strong>Date:</strong> <?= htmlspecialchars($purchase['event_date']) ?> |
                            <strong>Venue:</strong> <?= htmlspecialchars($purchase['event_venue']) ?>
                            <br>
                            <strong>Seat:</strong> <?= htmlspecialchars($purchase['seat_label']) ?> |
                            <strong>Ticket Price:</strong> $<?= htmlspecialchars($purchase['ticket_price']) ?>
                            <br>
                            <img src="<?= htmlspecialchars($purchase['event_image']) ?>" alt="Event Image" width="100">
                        </li>
                        <hr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No purchases found.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>