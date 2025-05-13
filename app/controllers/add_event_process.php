<?php
// Iniciamos la sesión
session_start();
require_once '../../config/database.php';

// Verificamos que el método sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $total_seats = $_POST['total_seats'];
    $image_url = $_POST['image_url'];

    try {
        // Insertamos el evento en la base de datos
        $stmt = $pdo->prepare("INSERT INTO events (title, event_date, venue, type, price, total_seats, img) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $date, $location, $type, $price, $total_seats, $image_url]);

        // Obtenemos el ID del evento recién insertado
        $eventId = $pdo->lastInsertId();

        // Verificamos si el evento tiene asientos numerados
        if ($type === 'Concert' || $type === 'Opera' ||
            $type === 'Theater' || $type === 'Conference' ||
            $type === 'Cinema') { // Ajusta los tipos según tu lógica
            $columns = 10; // Número de columnas por fila
            $alphabet = range('A', 'Z'); // Genera las letras de la A a la Z
            $rowIndex = 0; // Índice para las filas

            // Generamos los asientos
            for ($i = 1; $i <= $total_seats; $i++) {
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
                $seatLabel = $rowName . (($i % $columns == 0) ? $columns : $i % $columns);

                // Insertamos el asiento en la tabla `seats`
                $seatStmt = $pdo->prepare("INSERT INTO seats (event_id, seat_label, is_sold) VALUES (?, ?, ?)");
                $seatStmt->execute([$eventId, $seatLabel, 0]); // `is_sold` inicia en 0 (disponible)
            }
        }

        // Redirigimos al listado de eventos
        header("Location: ../../public/events.php?view=list&success=added");
        exit();
    } catch (PDOException $e) {
        // Mostrar el error de PDO
        die("Database Error: " . $e->getMessage());
    }
} else {
    header("Location: ../public/events.php");
    exit();
}