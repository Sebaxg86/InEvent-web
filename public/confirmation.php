<?php
if (!isset($_GET['order_id'])) {
    die("Orden no especificada.");
}
$orderId = htmlspecialchars($_GET['order_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Orden</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { width: 80%; margin: 50px auto; text-align: center; padding: 20px; background: #fff; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        a { text-decoration: none; color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Orden Completada</h1>
        <p>¡Gracias! Tu orden con ID <strong><?= $orderId ?></strong> se ha completado con éxito.</p>
        <p><a href="/events.php">Volver a inicio</a></p>
    </div>
</body>
</html>