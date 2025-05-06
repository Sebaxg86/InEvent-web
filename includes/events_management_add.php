<section class="welcome-message">
    <h2>Add New Event</h2>
</section>

<section class="content-container">
    <div class="content-form">
        <form action="add_event_process.php" method="POST" enctype="multipart/form-data">
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
    
            <label>Image:</label>
            <input type="file" name="image" accept="image/*" required><br><br>
    
            <button class="btn" type="submit">Save Event</button>

            <a href="events.php" class="btn-danger">Cancel</a>
        </form>
    </div>
</section>