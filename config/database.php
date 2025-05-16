<?php
// ======= Load Environment Variables ==========================
// This file retrieves environment variables that hold sensitive configuration data.
require_once __DIR__ . '/env.php';

// ======= Retrieve Database Configuration from Environment Variables =======
$host     = env('DB_HOST');
$dbname   = env('DB_NAME');
$username = env('DB_USER');
$password = env('DB_PASS');

try {
    // ======= Establish a PDO Database Connection ==========================
    // DSN (Data Source Name) is constructed with the host, database name, and charset.
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // ======= Set PDO Error Mode to Exception ==========================
    // This ensures that PDO throws exceptions on database errors.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // ======= Handle Connection Errors ==========================
    // If a connection error occurs, output the error message and terminate the script.
    die("Database connection error: " . $e->getMessage());
}