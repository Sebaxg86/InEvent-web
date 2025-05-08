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
    <link rel="stylesheet" href="css/events.css">
    <link rel="stylesheet" href="css/events_seats.css">

    <!--Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <title>InEvent</title>
</head>
<body>
    <!--Header-->
    <?php include_once '../includes/header.php'; ?>

    <!--Navbar-->
    <?php include_once '../includes/navbar.php'; ?>

    <main>
        <section class="seats-container">
            <h2>Select Your Seats</h2>
            <div class="seats-grid">
                <div class="row">
                    <div class="seat" data-seat="A1"><ion-icon name="person-outline"></ion-icon></div>
                    <div class="seat" data-seat="A2"><ion-icon name="person-outline"></ion-icon></div>
                    <div class="seat" data-seat="A3"><ion-icon name="person-outline"></ion-icon></div>
                    <div class="seat" data-seat="A4"><ion-icon name="person-outline"></ion-icon></div>
                </div>
                <div class="row">
                    <div class="seat" data-seat="B1"><ion-icon name="person-outline"></ion-icon></div>
                    <div class="seat" data-seat="B2"><ion-icon name="person-outline"></ion-icon></div>
                    <div class="seat" data-seat="B3"><ion-icon name="person-outline"></ion-icon></div>
                    <div class="seat" data-seat="B4"><ion-icon name="person-outline"></ion-icon></div>
                </div>
            </div>
            <div class="selection-info">
                <p>Selected Seats: <span id="selected-seats"></span></p>
                <p>Total: <span id="total-price">0 MXN</span></p>
            </div>
            <button class="btn" id="proceed-payment">Proceed to Payment</button>
        </section>

        <script src="../js/events_seats.js"></script>
    </main>

    <!--Footer-->
    <?php include_once "../includes/footer.php"; ?>

    <!--Ionic Icons Installation-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>