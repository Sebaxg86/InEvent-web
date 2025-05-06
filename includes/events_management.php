<?php
$view = $_GET['view'] ?? 'list';

if (!isset($_SESSION['user']) || $_SESSION['user']['is_admin'] != 1) {
    include_once 'events_list_public.php';
} else {
    if ($view === 'add') {
        include_once 'events_management_add.php';
    } else {
        include_once 'events_list_admin.php';
    }
}
?>