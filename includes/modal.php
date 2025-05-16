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