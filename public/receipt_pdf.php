<?php
if (!isset($_GET['order_id']) || !isset($_GET['event_id'])) {
    die("Datos insuficientes.");
}
$orderId = htmlspecialchars($_GET['order_id']);
$eventIdForJoin = htmlspecialchars($_GET['event_id']);

echo "<h1>El id del evento traido desde antes es: $eventIdForJoin</h1>";

require_once '../config/database.php';

// Consulta para obtener los detalles de la orden y el usuario
$stmt = $pdo->prepare("
    SELECT 
        o.id AS order_id,
        o.created_at,
        o.total,
        o.payment_method,
        u.name AS customer_name,
        u.email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.id = :orderId
");
$stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);
$stmt->execute();
$orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$orderDetails) {
    die("Orden no encontrada.");
}

// Consulta para obtener los tickets (asientos) de la orden.
// Se utiliza COALESCE para usar s.event_id cuando exista; de lo contrario se toma el event_id pasado.
$stmt2 = $pdo->prepare("
    SELECT 
        oi.id AS ticket_id,
        oi.price,
        IFNULL(s.seat_label, '-') AS seat_label,
        e.title AS event_name,
        e.event_date,
        e.venue AS location,
        e.img AS event_image
    FROM order_items oi
    LEFT JOIN seats s ON oi.seat_id = s.id
    LEFT JOIN events e ON e.id = COALESCE(s.event_id, :eventId)
    WHERE oi.order_id = :orderId
");
$stmt2->bindParam(':orderId', $orderId, PDO::PARAM_INT);
$stmt2->bindParam(':eventId', $eventIdForJoin, PDO::PARAM_INT);
$stmt2->execute();
$tickets = $stmt2->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <!--Styles-->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/receipt_pdf.css">

    <!--Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <title>Order Receipt</title>
</head>
<body>
    <h1>Thank you for your purchase! <strong>#<?= $orderDetails['order_id'] ?></strong></h1>
    <h2>Your tickets:</h2>

    <div class="container">
        <div class="order-details">
            <h2>Order Details</h2>
            <p><strong>Order ID:</strong> <?= $orderDetails['order_id'] ?></p>
            <p><strong>Date:</strong> <?= $orderDetails['created_at'] ?></p>
            <p><strong>Customer Name:</strong> <?= $orderDetails['customer_name'] ?></p>
            <p><strong>Email:</strong> <?= $orderDetails['email'] ?></p>
            <p><strong>Ticket Amount:</strong> <?= count($tickets) ?></p>
            <p><strong>Payment Method:</strong> <?= ucfirst($orderDetails['payment_method']) ?></p>
            <p><strong>Total Amount:</strong> $<?= $orderDetails['total'] ?></p>
        </div>

        <?php foreach($tickets as $ticket): ?>
            <div class="ticket">
                <h3>Ticket Details <?= (!empty($ticket['seat_label']) && $ticket['seat_label'] != '-') ? "(Seat: {$ticket['seat_label']})" : "" ?></h3>
                <p><strong>Image:</strong> <img src="uploads/<?= $ticket['event_image'] ?>" alt="Event Image" width="80"></p>
                <p><strong>Event:</strong> <?= $ticket['event_name'] ?></p>
                <p><strong>Date:</strong> <?= $ticket['event_date'] ?></p>
                <p><strong>Location:</strong> <?= $ticket['location'] ?></p>
                <p><strong>Price:</strong> $<?= $ticket['price'] ?></p>
                <p><strong>Ticket ID:</strong> <?= $ticket['ticket_id'] ?></p>
            </div>
        <?php endforeach; ?>

        <p><a href="events.php">Volver a inicio</a></p>
    </div>

    <!-- Incluir la librería html2pdf.js vía CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <!-- Incluir el archivo de lógica para generar PDF -->
    <script src="receipt_pdf_logic.js"></script>
</body>
</html>