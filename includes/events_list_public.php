<section class="browse-message">
    <h2>Browse Events</h2>
</section>

<section class="events-container">

    <div class="events-grid">
        <?php
        require_once '../config/database.php';
        $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
        while ($event = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <?php
                // Decide if this event uses numbered seats
                $usesSeats = in_array($event['type'], [
                    'Concert',
                    'Theater',
                    'Opera',
                    'Cinema',
                    'Conference'
                ]);
                $btnText = $usesSeats ? 'View Seats' : 'Buy Tickets';
            ?>

            <div class="event-card">
                <img src="<?php echo $event['img']; ?>" alt="<?php echo $event['title']; ?>">
                <div class="event-info">
                    <h3><?php echo $event['title']; ?></h3>
                    <p>Date: <?php echo date('F j, Y · g:i A', strtotime($event['event_date'])); ?></p>
                    <p>Location: <?php echo $event['venue']; ?></p>
                    <p>Type: <?php echo $event['type']; ?></p>
                    <p>From $<?php echo number_format($event['price'], 2); ?> MXN</p>
                    <a href="events_seats.php?id=<?php echo $event['id']; ?>" class="btn"><?php echo $btnText; ?></a>
                </div>
            </div>

        <?php endwhile; ?>
    </div>
</section>