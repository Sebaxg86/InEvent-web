<?php
require_once __DIR__ . '/env.php';

$host = env('DB_HOST');
$dbname = env('DB_NAME');
$username = env('DB_USER');
$password = env('DB_PASS');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection error: " . $e->getMessage());
}