<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php'; // Incluye la conexión a la base de datos

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
    if ($result) {
        $totalSeats = $result['total_seats']; // Total de asientos del evento
    } else {
        throw new Exception("Evento no encontrado.");
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

    <!--Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <title>InEvent</title>
</head>
<body>
    <!--Header-->
    <?php include_once '../includes/header.php'; ?>

    <!--Navbar-->
    <?php include_once '../includes/navbar.php'; ?>

    <p><?php echo $_SESSION['user']['is_guest']; ?> </p>

    <!--Seats selection-->
    <main class="seats-layout">
        <!--Seats container-->
        <div class="container" id="seats-grid" data-price="<?php echo $result['price']; ?>">
            <?php
            $columns = 10; // Número de columnas por fila
            $alphabet = range('A', 'Z'); // Genera las letras de la A a la Z
            $rowIndex = 0; // Índice para las filas

            for ($i = 1; $i <= $totalSeats; $i++) {
                // Calcula la fila actual
                if (($i - 1) % $columns == 0 && $i > 1) {
                    $rowIndex++;
                }

                // Genera el nombre de la fila (A, B, ..., Z, AA, AB, ...)
                $rowName = '';
                $tempIndex = $rowIndex;
                while ($tempIndex >= 0) {
                    $rowName = $alphabet[$tempIndex % 26] . $rowName;
                    $tempIndex = floor($tempIndex / 26) - 1;
                }

                // Genera el asiento con el formato "Fila + Número"
                $seatNumber = $i % $columns == 0 ? $columns : $i % $columns;
                echo 
                '<div class="seat" data-seat="' . $rowName . $seatNumber . '"> 
                    <ion-icon name="person-outline"></ion-icon>
                    <span>' . $rowName . $seatNumber . '</span>
                </div>';
            }
            ?>
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

            <button id="proceed-payment" class="btn" disabled>Proceed to Payment</button>
        </aside>
    </main>
    

    <!--Footer-->
    <?php include_once "../includes/footer.php"; ?>

    <!--Ionic Icons Installation-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../js/events_seats.js"></script>
<?php
?>
</body>
</html>