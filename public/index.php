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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bxslider@4.2.17/dist/jquery.bxslider.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bxslider@4.2.17/dist/jquery.bxslider.min.js"></script>

    <script>
      $(document).ready(function(){
        $(".slider").bxSlider();
      });
    </script>

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
    
        <!-- ===================== HERO / CAROUSEL ===================== -->
        <div class="carousel">
            <div class="slider">
                <div>
                    <img src="https://media-cdn.tripadvisor.com/media/attractions-splice-spp-674x446/10/7f/97/24.jpg" alt="Image 1">
                </div>
                <div>
                    <img src="https://cultivarte.mx/wp-content/uploads/2023/10/i-virtuosi-dell-opera.jpg" alt="Image 2">
                </div>
                <div>
                    <img src="https://s.abcnews.com/images/GMA/rufus-du-soul-01-ht-llr-221011_1665534667674_hpMain.jpg" alt="Image 3">
                </div>
                <div>
                    <img src="https://www.tadaima.com.mx/wp-content/uploads/2023/09/sns_1920_1080-1-1024x576.jpg" alt="Image 4">
                </div>  
                <div>
                    <img src="https://www.cameronhouse.co.uk/content/uploads/2024/03/camern-house-cinema.jpg" alt="Image 5">
                </div>
                <div>
                    <img src="https://static01.nyt.com/images/2017/10/17/science/11physed-marathon-photo/11physed-marathon-photo-superJumbo.jpg" alt="Image 6">
                </div>
            </div>
        </div>

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

    <script>
    document.getElementById('view-catalog-btn').addEventListener('click', () => {
        window.location.href = 'events.php'; // Redirige a events.php
    });
    </script>
</body>
</html>