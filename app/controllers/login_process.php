<?php
// ======= Start Session and Load Database Connection =======
session_start();
require_once '../../config/database.php';

// ======= Verify that the Request Method is POST =======
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ======= Retrieve Email and Password from POST Data =======
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // ======= Validate Required Fields =======
    if (!$email || !$password) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: ../../public/register.php?form=login");
        exit;
    }

    // ======= Validate Email Format =======
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: ../../public/register.php?form=login");
        exit;
    }

    try {
        // ======= Retrieve User Data from Database =======
        $stmt = $pdo->prepare("SELECT id, name, email, pass_hash, is_admin, is_guest FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // ======= Verify Password and Set Session Data =======
        if ($user && password_verify($password, $user['pass_hash'])) {
            $_SESSION['user'] = [
                'id'       => $user['id'],
                'name'     => $user['name'],
                'email'    => $user['email'],
                'is_admin' => $user['is_admin'],
                'is_guest' => $user['is_guest']
            ];
            header("Location: ../../public/index.php");
            exit;
        } else {
            // ======= Handle Invalid Credentials =======
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: ../../public/register.php?form=login");
            exit;
        }
    } catch (PDOException $e) {
        // ======= Handle Database Error =======
        echo "Database error: " . $e->getMessage();
        exit;
    }
} else {
    // ======= Redirect to Login Form if Request is Not POST =======
    header("Location: ../../public/register.php");
    exit;
}