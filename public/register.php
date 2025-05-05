<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InEvent</title>

    <!--Styles-->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/contact.css">

    <!--Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

    <!--Header-->
    <?php include_once '../includes/header.php'; ?>

    <!--Navbar-->
    <?php include_once '../includes/navbar.php'; ?>

    <!--Main-->
    <section class="welcome-message">
        <h2>Create Your Account</h2>
    </section>
    
    <div class="contact-container">
        <div class="contact-form">
            <form action="#" method="post">
                <label for="fullname">Full Name:</label><br>
                <input type="text" id="fullname" name="fullname" required><br><br>
    
                <label for="email">Email Address:</label><br>
                <input type="email" id="email" name="email" required><br><br>
    
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br><br>
    
                <label for="confirm-password">Confirm Password:</label><br>
                <input type="password" id="confirm-password" name="confirm-password" required><br><br>
    
                <button class="btn" type="submit">Register</button>
            </form>
        </div>
    </div>

    <!--Footer-->
    <?php include_once "../includes/footer.php"; ?>

    <!--Ionic Icons Installation-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>