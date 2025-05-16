<?php
// ======= Check if the User is Logged In =======
if (!isset($_SESSION['user'])) {
    // ======= Not logged in: Show public events list =======
    include_once 'events_list_public.php';
} elseif ($_SESSION['user']['is_admin'] == 1) {
    // ======= Logged In as Admin: Determine the Admin View =======
    $view = $_GET['view'] ?? 'list';
    if ($view === 'add') {
        // ======= Admin: Add New Event View =======
        include_once 'events_management_add.php';
    } elseif ($view === 'edit' && isset($_GET['id'])) {
        // ======= Admin: Edit Event View =======
        include_once 'events_management_edit.php';
    } else {
        // ======= Admin: Default to Events List =======
        include_once 'events_list_admin.php';
    }
} else {
    // ======= Logged In as Client: Show public events list =======
    include_once 'events_list_public.php';
}
?>