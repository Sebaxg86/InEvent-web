<?php
// Iniciamos la sesión
session_start();
require_once '../../config/database.php';

// Verificamos que el método sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    // Eliminamos el evento de la base de datos
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$event_id]);

    // Mensaje de éxito
    echo "Event deleted successfully.";

    // Redirigimos al listado de eventos
    header("Location: ../../public/events.php?view=list&success=deleted");
    exit();
} else {
    header("Location: ../public/events.php");
    exit();
}