<nav class="nav-bar">
    
    <div class="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <ul>
        <li><a href="index.php">Home <ion-icon name="home-outline"></ion-icon></a></li>

        <?php if (isset($_SESSION['user'])): ?>
            <?php if ($_SESSION['user']['is_admin'] == 1): ?>
                <li><a href="events.php">Manage Events <ion-icon name="construct-outline"></ion-icon></a></li>
                <li><a href="logout.php">Log Out <ion-icon name="log-out-outline"></ion-icon></a></li>
            <?php else: ?>
                <li><a href="events.php">Events <ion-icon name="ticket-outline"></ion-icon></a></li>
                <li><a href="about.php">About us <ion-icon name="information-circle-outline"></ion-icon></a></li>
                <li><a href="contact.php">Contact <ion-icon name="paper-plane-outline"></ion-icon></a></li>
                <li><a href="register.php">My Account <ion-icon name="person-outline"></ion-icon></a></li>
            <?php endif; ?>
        <?php else: ?>
            <li><a href="events.php">Events <ion-icon name="ticket-outline"></ion-icon></a></li>
            <li><a href="about.php">About us <ion-icon name="information-circle-outline"></ion-icon></a></li>
            <li><a href="contact.php">Contact <ion-icon name="paper-plane-outline"></ion-icon></a></li>
            <li><a href="register.php">Register <ion-icon name="log-in-outline"></ion-icon></a></li>
        <?php endif; ?>
    </ul>
</nav>