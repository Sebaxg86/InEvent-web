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
        oi.id AS ticket_id,
        oi.price AS ticket_price,
        IFNULL(s.seat_label, 'General') AS seat_label,
        e.title AS event_title,
        e.event_date,
        e.venue AS event_venue,
        e.img AS event_image
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN seats s ON oi.seat_id = s.id
    LEFT JOIN events e ON e.id = COALESCE(s.event_id, oi.event_id)
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

<div class="basic-info">
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
</div>

<hr>

<div class="purchases-title">
    <h2>Purchases</h2>
</div>

<div class="purchases">
    <div class="purchases-list">
        <?php if (!empty($purchases)): ?>
            <?php foreach ($purchases as $purchase): ?>
                <div class="ticket-card">
                    <img src="<?= htmlspecialchars($purchase['event_image']) ?>" alt="Event Image">
                    <h4 class="event-title"><?= htmlspecialchars($purchase['event_title']) ?></h4>
                    
                    <div class="date">
                        <p><strong>Date:</strong></p>
                        <p><?= htmlspecialchars($purchase['event_date']) ?></p>
                    </div>
                
                    <div class="venue">
                        <p><strong>Venue:</strong></p>
                        <p><?= htmlspecialchars($purchase['event_venue']) ?></p>
                    </div>

                    <div class="seat">
                        <p><strong>Seat:</strong></p>
                        <p><?= htmlspecialchars($purchase['seat_label']) ?></p>
                    </div>
                    
                    <div class="price">
                        <p><strong>Price:</strong></p>
                        <p>$<?= htmlspecialchars($purchase['ticket_price']) ?></p>
                    </div>

                    <div class="order-id">
                        <p><strong>Order ID:</strong></p>
                        <p><?= htmlspecialchars($purchase['order_id']) ?></p>
                    </div>

                    <div class="ticket-id">
                        <p><strong>Ticket ID:</strong></p>
                        <p><?= htmlspecialchars($purchase['ticket_id']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No purchases found.</p>
        <?php endif; ?>
    </div>
</div>