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
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/carousel.css">
    
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
    
<!---- CAROUSEL – PEGAR DENTRO DEL <body> --->
<section class="hero-carousel" aria-label="Galería promocional">
  <div class="carousel-track">
    <!-- 6 imág.  (reemplaza data-src por tu URL) -->
    <figure class="slide" data-src="https://media-cdn.tripadvisor.com/media/attractions-splice-spp-674x446/10/7f/97/24.jpg"  aria-label="Imagen 1"></figure>
    <figure class="slide" data-src="https://cultivarte.mx/wp-content/uploads/2023/10/i-virtuosi-dell-opera.jpg"  aria-label="Imagen 2"></figure>
    <figure class="slide" data-src="https://s.abcnews.com/images/GMA/rufus-du-soul-01-ht-llr-221011_1665534667674_hpMain.jpg"  aria-label="Imagen 3"></figure>
    <figure class="slide" data-src="https://world-masters-athletics.org/wp-content/uploads/2023/08/Bucharest_Marathon-scaled.jpg"  aria-label="Imagen 4"></figure>
    <figure class="slide" data-src="https://i.ytimg.com/vi/hY23w-rbarI/maxresdefault.jpg"  aria-label="Imagen 5"></figure>
    <figure class="slide" data-src="https://images.adsttc.com/media/images/58d5/3a58/e58e/ce48/a700/003f/large_jpg/002.jpg?1490369108"  aria-label="Imagen 6"></figure>
  </div>

  <!-- Puntitos de progreso -->
  <div class="carousel-nav" role="tablist" aria-label="Paginación">
    <button class="nav-dot" aria-label="Ir a la 1" role="tab"></button>
    <button class="nav-dot" aria-label="Ir a la 2" role="tab"></button>
    <button class="nav-dot" aria-label="Ir a la 3" role="tab"></button>
    <button class="nav-dot" aria-label="Ir a la 4" role="tab"></button>
    <button class="nav-dot" aria-label="Ir a la 5" role="tab"></button>
    <button class="nav-dot" aria-label="Ir a la 6" role="tab"></button>
  </div>
</section>

        <!--View Catalog Button-->
        <div class="btn-div">
            <button class="btn" id="view-catalog-btn">View Catalog</button>
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
    <script src="js/carrousel.js"></script>

    <script>
    document.getElementById('view-catalog-btn').addEventListener('click', () => {
        window.location.href = 'events.php'; // Redirige a events.php
    });
    </script>
    <script src="../js/carrousel.js"></script>
</body>
</html>