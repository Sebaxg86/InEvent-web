<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--Styles-->
    <link rel="stylesheet" href="css/styles.css">

    <!--Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <title>InEvent</title>
</head>
<body>
    <!--Header-->
    <?php include_once '../includes/header.php'; ?>

    <!--Main-->
    <main>

        <!--Navbar-->
        <?php include_once '../includes/navbar.php'; ?>
    
        <!--Welcome Message-->
        <section class="welcome-message">
            <h2>Experience every event as if you were IN the front row</h2>
            <p>Discover unforgettable moments. Book your tickets now, wherever you are.</p>
        </section>
    
        <!-- Carousel -->
        <section class="carousel">
            <div class="carousel-track">
                <img src="assets/img/concert.png" alt="Concert" class="carousel-img">
                <img src="assets/img/convention.png" alt="Travel" class="carousel-img">
                <img src="assets/img/Flight.png" alt="Event" class="carousel-img">
            </div>
            <button class="carousel-btn prev">&#10094;</button>
            <button class="carousel-btn next">&#10095;</button>
        </section>

        <!--View Catalog Button-->
        <div class="btn-div">
            <button class="btn">View Catalog</button>
        </div>
    
        <!--Features Section-->
        <section class="feature-section">
            <div>
                <img src="">
                <h3 class="feature-section-title">Fast <br> Buy</h3>
                <div class="feature-section-icon">
                    <ion-icon style="font-size: 128px;" name="speedometer-outline"></ion-icon>
                </div>
                <div>
                    <p>
                        <span><b>Get your tickets in seconds.</b></span><br>
                        Simple, fast, and hassle-free checkout.
                    </p>
                </div>
            </div>
            <div>
                <img src="">
                <h3 class="feature-section-title">100% <br> Secure</h3>
                <div class="feature-section-icon">
                    <ion-icon style="font-size: 128px;" name="lock-closed-outline"></ion-icon>
                </div>
                <div>
                    <p>
                        <span><b>Shop with total confidence.</b></span><br>
                        Your data is protected with top-level security.
                    </p>
                </div>
            </div>
            <div>
                <img src="">
                <h3 class="feature-section-title">Access Anywhere</h3>
                <div class="feature-section-icon">
                    <ion-icon style="font-size: 128px;" name="earth-outline"></ion-icon>
                </div>
                <div>
                    <p>
                        <span><b>Join events from anywhere.</b></span><br>
                        Desktop, tablet, or mobile — you choose.
                    </p>
                </div>
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