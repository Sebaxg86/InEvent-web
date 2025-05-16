<!--===========================-->
<?php
session_start();

$form_type = $_GET['form'] ?? 'login'; // default = login
?>

<!--===========================-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InEvent</title>

    <!--Styles-->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/register_account.css">
    <link rel="stylesheet" href="css/user_info.css">
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="css/account.css">

    <!--Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

    <!--Header-->
    <?php include_once '../includes/header.php'; ?>

    <!--Navbar-->
    <?php include_once '../includes/navbar.php'; ?>

    <!--Main-->
    <?php
        if (isset($_SESSION['user'])) {
            include_once '../includes/user_info.php';
        } else {
            if ($form_type === 'register') {
                include_once '../includes/register_account.php';
            } else {
                include_once '../includes/login.php';
            }
        }
    ?>

    <!--Footer-->
    <?php include_once "../includes/footer.php"; ?>

    <?php
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
    echo "<script>
            window.addEventListener('DOMContentLoaded', function() {
                showError('".htmlspecialchars($error, ENT_QUOTES)."');
            });
          </script>";
}
?>

    <!-- Popup de error -->
    <div id="error-popup" class="error-popup">
        <div class="error-popup-content">
            <span class="error-popup-close">&times;</span>
            <p id="error-message"></p>
        </div>
    </div>

    <script src="../js/modal.js"></script>

    <!--Ionic Icons Installation-->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>