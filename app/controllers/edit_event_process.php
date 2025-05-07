<?php
// Iniciamos la sesión
session_start();
require_once '../config/database.php';

// Verificamos que el método sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $title = $_POST['title'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $price = $_POST['price'];

    // Verificamos si se subió una nueva imagen
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_dir = "../public/assets/img/";
        $target_file = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            header("Location: ../public/events.php?view=edit&id=$event_id&error=upload_failed");
            exit();
        }
    }

    // Preparamos la consulta de actualización
    if ($image_name) {
        $stmt = $pdo->prepare("UPDATE events SET title = ?, event_date = ?, venue = ?, type = ?, price = ?, img = ? WHERE id = ?");
        $stmt->execute([$title, $date, $location, $type, $price, 'assets/img/' . $image_name, $event_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE events SET title = ?, event_date = ?, venue = ?, type = ?, price = ? WHERE id = ?");
        $stmt->execute([$title, $date, $location, $type, $price, $event_id]);
    }

    // Redirigimos al listado de eventos
    header("Location: ../public/events.php?view=list&success=edited");
    exit();
} else {
    header("Location: ../public/events.php");
    exit();
}