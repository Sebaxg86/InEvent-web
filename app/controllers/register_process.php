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

    // Validar el formato del correo electrónico
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $_SESSION['error'] = "Invalid email format.";
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

        $stmt = $pdo->prepare("INSERT INTO users (name, email, pass_hash, is_admin, is_guest) VALUES (:name, :email, :pass, :is_admin, :is_guest)");
        $stmt->execute([
            'name' => $fullname,
            'email' => $email,
            'pass' => $hashed_password,
            'is_admin' => 0,
            'is_guest' => 0
        ]);

        $_SESSION['user'] = [
            'id' => $pdo->lastInsertId(),
            'name' => $fullname,
            'email' => $email,
            'is_admin' => 0,
            'is_guest' => 0
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