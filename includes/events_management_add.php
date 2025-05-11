<section class="welcome-message">
    <h2>Add New Event</h2>
</section>

<section class="content-container">
    <div class="content-form">
        <form action="../app/controllers/add_event_process.php" method="POST">
            <label>Title:</label>
            <input type="text" name="title" required><br>
    
            <label>Date:</label>
            <input type="datetime-local" name="date" required><br>
    
            <label>Location:</label>
            <input type="text" name="location" required><br>
    
            <label>Type:</label>
            <select name="type" required>
                <option value="Concert">Concert (With Seats)</option>
                <option value="Concert (Standing)">Concert (Standing)</option>
                <option value="Theater">Theater</option>
                <option value="Opera">Opera</option>
                <option value="Conference">Conference</option>
                <option value="Marathon">Marathon</option>
                <option value="Anime Convention">Anime Convention</option>
                <option value="Formula 1">Formula 1</option>
            </select>
            <br>
    
            <label>Price (MXN):</label>
            <input type="number" name="price" required><br>

            <label>Total Seats:</label>
            <input type="number" name="total_seats" required><br>
    
            <label>Image URL:</label>
            <input type="url" name="image_url" required><br><br>
    
            <button class="btn" type="submit">Save Event</button>
            <a href="events.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</section>