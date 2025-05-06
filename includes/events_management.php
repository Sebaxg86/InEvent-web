<?php
$view = $_GET['view'] ?? 'list'; // list, add, edit
$event_id = $_GET['id'] ?? null;

if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    include_once 'events_list_public.php'; 
} else {
    // Admin logged in
    switch ($view) {
        case 'add':
            include_once 'events_form_add.php';
            break;
        case 'edit':
            include_once 'events_form_edit.php'; // usar $event_id si lo necesitas
            break;
        default:
            include_once 'events_list_admin.php';
            break;
    }
}
?>