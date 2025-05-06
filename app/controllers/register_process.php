<?php
session_start();
require_once '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';

    if (!$fullname || !$email || !$password || !$confirm_password) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: ../../public/register.php?form=register");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../../public/register.php?form=register");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "This email is already registered.";
            header("Location: ../../public/register.php?form=register");
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, pass_hash) VALUES (:name, :email, :pass)");
        $stmt->execute([
            'name' => $fullname,
            'email' => $email,
            'pass' => $hashed_password
        ]);

        $_SESSION['user'] = [
            'id' => $pdo->lastInsertId(),
            'name' => $fullname,
            'email' => $email
        ];

        header("Location: ../../public/register.php");
        exit;
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
        exit;
    }
} else {
    header("Location: ../../public/register.php");
    exit;
}