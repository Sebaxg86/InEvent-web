<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $title = $_POST['title'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $total_seats = $_POST['total_seats'];
    $image_url = $_POST['image_url'];

    try {
        // Actualizamos el evento
        $stmt = $pdo->prepare("UPDATE events SET title = ?, event_date = ?, venue = ?, type = ?, price = ?, total_seats = ?, img = ? WHERE id = ?");
        $stmt->execute([$title, $date, $location, $type, $price, $total_seats, $image_url, $event_id]);

        // Redirigimos al listado de eventos con éxito
        header("Location: ../../includes/events_list_admin.php?success=edited");
        exit();
    } catch (Exception $e) {
        // Mostramos cualquier error
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: ../../includes/events_list_admin.php?error=invalid_request");
    exit();
}