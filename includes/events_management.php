<?php
// Verificamos si el usuario está logueado
if (!isset($_SESSION['user'])) {
    include_once 'events_list_public.php'; // Vista para invitados
} elseif ($_SESSION['user']['is_admin'] == 1) {
    // Vista para administradores
    $view = $_GET['view'] ?? 'list';
    if ($view === 'add') {
        include_once 'events_management_add.php';
    } elseif ($view === 'edit' && isset($_GET['id'])) {
        include_once 'events_management_edit.php';
    } else {
        include_once 'events_list_admin.php';
    }
} else {
    // Vista para clientes
    include_once 'events_list_public.php';
}
?>