<section class="welcome-message">
    <h2>Edit Event</h2>
</section>

<section class="content-container">
    <div class="content-form">
        <form action="../app/controllers/edit_event_process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="event_id" value="<?php echo $_GET['id']; ?>">

            <label>Title:</label>
            <input type="text" name="title" value="Event Title" required><br>
    
            <label>Date:</label>
            <input type="datetime-local" name="date" required><br>
    
            <label>Location:</label>
            <input type="text" name="location" required><br>
    
            <label>Type:</label>
            <input type="text" name="type" required><br>
    
            <label>Price (MXN):</label>
            <input type="number" name="price" required><br>
    
            <label>Image (Leave blank to keep current):</label>
            <input type="file" name="image" accept="image/*"><br><br>
    
            <button class="btn" type="submit">Save Changes</button>
            <a href="events.php" class="btn btn-danger">Cancel</a>
        </form>
    </div>
</section>