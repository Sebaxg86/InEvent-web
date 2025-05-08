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
            <input type="text" name="type" required><br>
    
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