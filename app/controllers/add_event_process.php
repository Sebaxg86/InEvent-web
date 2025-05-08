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