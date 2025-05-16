<?php
// ======= PHP: Check for an Error in the Session =======
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
    // ======= PHP: Output a script that shows the error popup on DOMContentLoaded =======
    echo "<script>
            window.addEventListener('DOMContentLoaded', function() {
                showError('" . htmlspecialchars($error, ENT_QUOTES) . "');
            });
          </script>";
}
?>

    <!-- Error POPUP -->
    <div id="error-popup" class="error-popup">
        <div class="error-popup-content">
            <span class="error-popup-close">&times;</span>
            <p id="error-message"></p>
        </div>
    </div>