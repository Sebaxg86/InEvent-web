<?php
session_start();
require_once '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: ../../public/register.php?form=login");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, name, email, pass_hash FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['pass_hash'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ];
            header("Location: ../../public/register.php");
            exit;
        } else {
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: ../../public/register.php?form=login");
            exit;
        }
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        exit;
    }
} else {
    header("Location: ../../public/register.php");
    exit;
}