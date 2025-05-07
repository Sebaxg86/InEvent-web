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

    // Manejo de imagen
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_dir = "../public/assets/img/";
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Insertamos el evento en la base de datos
            $stmt = $pdo->prepare("INSERT INTO events (title, event_date, venue, type, price, img) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $date, $location, $type, $price, 'assets/img/' . $image_name]);

            // Redirigimos al listado de eventos
            header("Location: ../public/events.php?view=list&success=added");
            exit();
        } else {
            header("Location: ../public/events.php?view=add&error=upload_failed");
            exit();
        }
    } else {
        header("Location: ../public/events.php?view=add&error=no_image");
        exit();
    }
} else {
    header("Location: ../public/events.php");
    exit();
}