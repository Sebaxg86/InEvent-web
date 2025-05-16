<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Cancelled</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .container h1 {
            font-size: 2rem;
            color: #e74c3c;
            margin-bottom: 1rem;
        }
        .container p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            background: #3498db;
            color: #fff;
            padding: 0.8rem 1.5rem;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s ease;
        }
        .btn:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Purchase cancelled</h1>
        <p>Your purchase has been cancelled.</p>
        <a href="events.php" class="btn">Home</a>
    </div>
</body>
</html>