<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php'; // Incluye la conexión a la base de datos

//Determinar si el usuario está registrado
$isGuest = !isset($_SESSION['user']);

// Obtén el ID del evento desde la URL
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($eventId === 0) {
    die("Evento no válido.");
}

try {
    // Consulta para obtener toda la información del evento
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :eventId");
    $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
    $stmt->execute();

    // Obtén el resultado
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        throw new Exception("Evento no encontrado.");
    }

    // Verificar si el evento tiene asientos numerados
    $usesSeats = in_array($result['type'], [
        'Concert',
        'Theater',
        'Opera',
        'Cinema',
        'Conference'
    ]);

    // Si el evento usa asientos, obtener los asientos de la base de datos
    $seats = [];
    if ($usesSeats) {
        $seatStmt = $pdo->prepare("SELECT * FROM seats WHERE event_id = :eventId");
        $seatStmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $seatStmt->execute();
        $seats = $seatStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    die("Error al obtener la información del evento: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!--Styles-->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/events.css">
    <link rel="stylesheet" href="css/events_seats.css">
    <link rel="stylesheet" href="css/guest.css">

    <!--Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <title>InEvent</title>
</head>
<body class="<?php echo $isGuest ? 'guest' : 'logged-in'; ?>">
    <!--Header-->
    <?php include_once '../includes/header.php'; ?>

    <!--Navbar-->
    <?php include_once '../includes/navbar.php'; ?>

    <p><?php echo $_SESSION['user']['is_guest']; ?> </p>

    <!--Seats selection-->
    <?php if ($usesSeats): ?>
        <main class="seats-layout">
            <!--Seats container-->
            <div class="container" id="seats-grid" data-price="<?php echo $result['price']; ?>">
                <?php foreach ($seats as $seat): ?>
                    <div class="seat <?php echo $seat['is_sold'] ? 'sold' : ''; ?>" 
                        data-seat="<?php echo $seat['seat_label']; ?>" 
                        data-sold="<?php echo $seat['is_sold']; ?>">
                        <ion-icon name="person-outline"></ion-icon>
                        <span><?php echo $seat['seat_label']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <!--Event Resume Panel-->
            <aside class="summary-panel">
                <img src="<?php echo $result['img']; ?>" alt="<?php echo $result['title']; ?>" class="event-image">
                <h2><?php echo $result['title']; ?></h2>
                <p><strong>Date:</strong> <?php echo date('F j, Y · g:i A', strtotime($result['event_date'])); ?></p>
                <p><strong>Location:</strong> <?php echo $result['venue']; ?></p>
                <p><strong>Type:</strong> <?php echo $result['type']; ?></p>
                <p><strong>Price per seat:</strong> $<?php echo number_format($result['price'], 2); ?> MXN</p>

                <hr>

                <h3>Selected Seats</h3>
                <ul id="selected-seats-list">
                    <li>—</li>
                </ul>
                <p><strong>Total:</strong> <span id="total-price">0.00 MXN</span></p>
                <?php if($isGuest): ?>
                    <hr>

                    <div class="guest-container">
                        <h3>Purchasing as a Guest?</h3>
                        <h4>Provide an your email</h4><br>
                        <div class="guest-form">
                            <form id="guest-checkout-form">
                                <label for="guest-email">Email:</label><br>
                                <input type="email" id="guest-email" required><br><br>
    
                                <label for="guest-email-confirm">Confirm Email:</label><br>
                                <input type="email" id="guest-email-confirm" required><br>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
                <button id="proceed-payment" class="btn btn-payment" disabled>Proceed to Payment</button>
            </aside>
        </main>
    <?php else: ?>
        <div class="no-seats-layout">
            <!--Event Resume Panel-->
            <aside class="summary-panel">
                <img src="<?php echo $result['img']; ?>" alt="<?php echo $result['title']; ?>" class="event-image">
                <h2><?php echo $result['title']; ?></h2>
                <p><strong>Date:</strong> <?php echo date('F j, Y · g:i A', strtotime($result['event_date'])); ?></p>
                <p><strong>Location:</strong> <?php echo $result['venue']; ?></p>
                <p><strong>Type:</strong> <?php echo $result['type']; ?></p>
                <p><strong>Price per ticket:</strong> $<?php echo number_format($result['price'], 2); ?> MXN</p>
                <p><strong>Stock available:</strong> <?php echo $result['total_seats']; ?> tickets</p>
        
                <hr>

                <!--Ticket quantity spinner-->
                <div class="custom-spinner" data-total-seats="<?php echo $result['total_seats']; ?>" data-price="<?php echo $result['price']; ?>">
                    <button id="decrease-btn" class="spinner-btn">-</button>
                    <span id="ticket-quantity-display">1</span>
                    <button id="increase-btn" class="spinner-btn">+</button>
                </div>
        
                <p><strong>Total:</strong> <span id="total-price">0.00 MXN</span></p>
                
                <?php if($isGuest): ?>
                    <hr>

                    <div class="guest-container">
                        <h3>Purchasing as a Guest?</h3>
                        <h4>Provide an email</h4><br>
                        <div class="guest-form">
                            <form>
                                <label for="guest-email">Email:</label><br>
                                <input type="email" id="guest-email" name="guest-email" required><br><br>

                                <label for="guest-email-confirm">Confirm Email:</label><br>
                                <input type="email" id="guest-email-confirm" name="guest-email-confirm" required><br>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <button id="proceed-payment" class="btn" disabled>Proceed to Payment</button>
            </aside>
        </div>
    <?php endif; ?>

    <!--Footer-->
    <?php include_once "../includes/footer.php"; ?>

    <!--Ionic Icons Installation-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!--Scripts-->
    <script src="../js/events_seats.js"></script>
    <?php if ($usesSeats): ?>
        <script src="../js/guest_seated_payment_logic.js"></script>
    <?php else: ?>
        <script src="../js/guest_noSeats_payment_logic.js"></script>
    <?php endif; ?>
<?php
?>
</body>
</html>