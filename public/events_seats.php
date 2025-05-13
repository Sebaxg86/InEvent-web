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
    // Consulta para obtener el total de asientos del evento
    $stmt = $pdo->prepare("SELECT total_seats FROM events WHERE id = :eventId");
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
    die("Error al obtener el total de asientos: " . $e->getMessage());
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

    <!--Seats selection-->
    
    <main>
        <div class="container">
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
                '<div class="seat"> 
                    <ion-icon name="person-outline"></ion-icon>
                    ' . $rowName . $seatNumber . '
                </div>';
            }
            ?>
        </div>
    </main>
    

    <!--Footer-->
    <?php include_once "../includes/footer.php"; ?>

    <!--Ionic Icons Installation-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<?php
?>
</body>
</html>