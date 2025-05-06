<section class="welcome-message">
        <h2>Manage Events</h2>
</section>

<section>
    <div class="events-container">
        <div class="btn-div">
            <a href="events.php?view=add" class="btn" style="margin-top:2.5rem; margin-bottom: 2rem;">+ Add New Event</a>
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

            <div class="event-card">
                <img src="assets/img/interestellar.png" alt="Interstellar Movie">
                <h3>Interstellar - Special IMAX Night</h3>
                <p>Date: April 5, 2025 · 9:00 PM</p>
                <p>Location: Cinepolis VIP, CDMX</p>
                <p>Type: Movie</p>
                <p>From $180 MXN</p>
                <a href="events.php?view=edit&id=2" class="btn">Edit</a>
                <form method="POST" action="delete_event.php" onsubmit="return confirm('Are you sure you want to delete this event?');">
                    <input type="hidden" name="event_id" value="2">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>

            <!-- Más eventos aquí... -->
        </div>
    </div>
</section>