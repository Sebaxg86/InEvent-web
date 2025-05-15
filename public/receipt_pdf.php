<?php
if (!isset($_GET['order_id']) || !isset($_GET['event_id'])) {
    die("Datos insuficientes.");
}
$orderId = htmlspecialchars($_GET['order_id']);
$eventIdForJoin = htmlspecialchars($_GET['event_id']);

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
    <link rel="stylesheet" href="css/receipt_pdf.css?v=<?=time();?>">
    <link rel="stylesheet" href="css/styles.css">

    <!--Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <title>Order Receipt</title>
</head>
<body>
    <div class="thankyou-message">
        <h1>Thank you for your purchase!</h1>
    </div>

    <div class="btn-container">
        <button class="btn-download" onclick="window.location.href='index.php'"><ion-icon name="arrow-undo-outline" class="home"></ion-icon>Home</button>
        <button id="save-pdf" class="btn-download" onclick="downloadPDF()">Save Receipt<ion-icon name="cloud-download-outline" class="save"></ion-icon></button>
    </div>

    <?php include_once "../includes/receipt_pdf_content.php"; ?>
    
    <!--Ionic Icons Installation-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <script>
    function downloadPDF() {
        const orderId = <?= json_encode($orderId) ?>;
        const eventId = <?= json_encode($eventIdForJoin) ?>;
        const url = `../app/controllers/generate_pdf.php?order_id=${orderId}&event_id=${eventId}`;
        window.location.href = url; // Redirige al archivo generate_pdf.php
    }
</script>
</body>
</html>