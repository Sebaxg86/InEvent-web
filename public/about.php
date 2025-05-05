<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!--Styles-->
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/styles.css">

    <!--Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <title>InEvent</title>
</head>
<body>
    <!--Header-->
    <?php include_once '../includes/header.php'; ?>

    <!--Navbar-->
    <?php include_once '../includes/navbar.php'; ?>

    <!-- About Us -->
    <section class="welcome-message">
        <h2>About Us</h2>
        <p style="width: 60%; margin: 0 auto">At InEvent, we connect people with unforgettable experiences by making event ticketing simple, secure, and accessible. Whether it's a concert, theater, or special event, our mission is to bring excitement closer to you.</p>
    </section>

    <!--Main-->
    <main>

        <hr>

        <!-- Our Mission -->
        <section>
            <div>
                <img src="assets/img/mission.png" alt="Our Mission" width="300">
            </div>
            <div>
                <h3>Our Mission</h3>
                <p>To provide a seamless and reliable platform for purchasing tickets to the most exciting events around the world, ensuring every user has access to entertainment they love.</p>
            </div>
        </section>

        <!-- Our Vision -->
        <section>
            <div>
                <h3>Our Vision</h3>
                <p>To become the leading digital event gateway in Latin America, redefining how people discover and access live experiences.</p>
            </div>
            <div>
                <img src="assets/img/vision.png" alt="Our Vision" width="300">
            </div>
        </section>

        <!-- Our Values -->
        <section>
            <div>
                <img src="assets/img/values.png" alt="Our Values" width="300">
            </div>
            <div>
                <h3>Our Values</h3>
                <ul>
                    <li>🎯 Integrity</li>
                    <li>🤝 Customer-Centric</li>
                    <li>🚀 Innovation</li>
                    <li>🌎 Accessibility</li>
                </ul>
            </div>
        </section>
    </main>

    <!--Footer-->
    <?php include_once "../includes/footer.php"; ?>

    <!--Ionic Icons Installation-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>