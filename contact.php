<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!--Styles-->
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="css/styles.css">

    <!--Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <title>InEvent</title>
</head>
<body>
    <!--Header-->
    <?php include_once 'includes/header.php'; ?>

    <!--Navbar-->
    <?php include_once 'includes/navbar.php'; ?>

    <!--Main-->
    <section class="welcome-message">
        <h2>Contact Support</h2>
        <p>If you have any questions, suggestions, or issues, feel free to reach out to us using the form below.</p>
    </section>
        
    <div class="contact-container">
        <div class="contact-form">

                <form action="#" method="post">
                    <label for="email">Your Email:</label><br>
                    <input type="email" id="email" name="email" required><br><br>

                    <label for="subject">Message Subject:</label><br>
                    <input type="text" id="subject" name="subject" required><br><br>

                    <label for="message">Message:</label><br>
                    <textarea id="message" name="message" rows="6" required></textarea><br><br>

                    <button type="submit" class="btn">Send Message</button>
                </form>
        </div>
    </div>

    <!--Footer-->
    <?php include_once "includes/footer.php"; ?>

    <!--Ionic Icons Installation-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>