<?php
require_once '../config/database.php';

// Verificamos si se proporcionó un ID de evento
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        echo "Event not found.";
        exit();
    }
} else {
    echo "No event ID provided.";
    exit();
}
?>

<section class="welcome-message">
    <h2>Edit Event</h2>
</section>

<section class="content-container">
    <div class="content-form">
        <form action="../app/controllers/edit_event_process.php" method="POST">
            <input type="hidden" name="event_id" value="<?php echo $_GET['id']; ?>">

            <label>Title:</label>
            <input type="text" name="title" value="<?php echo $event['title']; ?>" required><br>
    
            <label>Date:</label>
            <input type="datetime-local" name="date" value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_date'])); ?>" required><br>
    
            <label>Location:</label>
            <input type="text" name="location" value="<?php echo $event['venue']; ?>" required><br>
    
            <label>Type:</label>
            <input type="text" name="type" value="<?php echo $event['type']; ?>" required><br>
    
            <label>Price (MXN):</label>
            <input type="number" name="price" value="<?php echo $event['price']; ?>" required><br>

            <label>Total Seats:</label>
            <input type="number" name="total_seats" value="<?php echo $event['total_seats']; ?>" required><br>
    
            <label>Image URL:</label>
            <input type="url" name="image_url" value="<?php echo $event['img']; ?>" required><br><br>
    
            <button class="btn" type="submit">Save Changes</button>
            <a href="events.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</section>