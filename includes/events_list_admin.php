<section class="welcome-message">
    <h2>Manage Events</h2>
</section>

<section class="events-container">
    <div class="btn-div">
        <a href="events.php?view=add" class="btn" style="margin-top: 2rem;">+ Add Event</a>
    </div>

    <div class="events-grid">
        <?php
        require_once '../config/database.php';
        $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
        while ($event = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="event-card">
                <img src="<?php echo $event['img']; ?>" alt="<?php echo $event['title']; ?>" style="width:100%; height:auto;">
                <h3><?php echo $event['title']; ?></h3>
                <p>Date: <?php echo date('F j, Y · g:i A', strtotime($event['event_date'])); ?></p>
                <p>Location: <?php echo $event['venue']; ?></p>
                <p>Type: <?php echo $event['type']; ?></p>
                <p>From $<?php echo number_format($event['price'], 2); ?> MXN</p>
                <form method="POST" action="../app/controllers/delete_event.php" onsubmit="return confirm('Are you sure you want to delete this event?');">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</section>