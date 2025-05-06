<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    // Si no hay sesión o el usuario no es admin (es cliente o guest)
    include_once 'events_list_public.php';
} else {
    // Si el usuario es admin
    include_once 'events_list_admin.php';
}
?>