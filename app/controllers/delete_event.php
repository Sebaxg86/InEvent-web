<?php
// ======= Start Session =======
session_start();

// ======= Load Database Connection =======
require_once '../../config/database.php';

// ======= Verify Request Method and Required Data =======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    // ======= Retrieve Event ID from POST Data =======
    $event_id = $_POST['event_id'];

    // ======= Delete the Event from the Database =======
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$event_id]);

    // ======= Output Success Message (may not be seen due to redirect) =======
    echo "Event deleted successfully.";

    // ======= Redirect to the Events List with Success Flag =======
    header("Location: ../../events.php?view=list&success=deleted");
    exit();
} else {
    // ======= Redirect to the Events List if Request is Invalid =======
    header("Location: ../events.php");
    exit();
}