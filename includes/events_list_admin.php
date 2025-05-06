<section class="welcome-message">
        <h2>Manage Events</h2>
</section>

<section>
    <div class="events-container">
        <div class="btn-div">
            <a href="../public/events.php?view=add" class="btn" style="margin-top: 2rem; margin-bottom: 1.5rem">+ Add Event</a>
        </div>

        <div class="events-grid">
            <div class="event-card">
                <img src="assets/img/coldplay.png" alt="Coldplay Concert">
                <h3>Coldplay Live in Monterrey</h3>
                <p>Date: March 30, 2025 · 8:00 PM</p>
                <p>Location: Estadio BBVA, Monterrey</p>
                <p>Type: Concert</p>
                <p>From $550 MXN</p>
                <a href="events.php?view=edit&id=1" class="btn">Edit</a>
                <form method="POST" action="delete_event.php" onsubmit="return confirm('Are you sure you want to delete this event?');">
                    <input type="hidden" name="event_id" value="1">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</section>

