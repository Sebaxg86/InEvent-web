<?php
// ======= Start Session and Load Database Connection =======
session_start();
require_once '../../config/database.php';

// ======= Verify that the Request Method is POST =======
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ======= Retrieve and Sanitize Form Data =======
    $fullname         = trim($_POST['fullname'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';

    // ======= Check for Missing Fields =======
    if (!$fullname || !$email || !$password || !$confirm_password) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: ../../register.php?form=register");
        exit;
    }

    // ======= Validate Email Format =======
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: ../../register.php?form=register");
        exit;
    }

    // ======= Verify Matching Passwords =======
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../../register.php?form=register");
        exit;
    }

    try {
        // ======= Check if the User Already Exists =======
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "This email is already registered.";
            header("Location: ../../register.php?form=register");
            exit;
        }

        // ======= Hash the Password =======
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // ======= Insert New User into the Database =======
        $stmt = $pdo->prepare("INSERT INTO users (name, email, pass_hash, is_admin, is_guest) VALUES (:name, :email, :pass, :is_admin, :is_guest)");
        $stmt->execute([
            'name'     => $fullname,
            'email'    => $email,
            'pass'     => $hashed_password,
            'is_admin' => 0,
            'is_guest' => 0
        ]);

        // ======= Set Session Data for the Newly Registered User =======
        $_SESSION['user'] = [
            'id'       => $pdo->lastInsertId(),
            'name'     => $fullname,
            'email'    => $email,
            'is_admin' => 0,
            'is_guest' => 0
        ];

        // ======= Redirect to the Registration Page or Dashboard =======
        header("Location: ../../register.php");
        exit;
    } catch (PDOException $e) {
        // ======= Handle Database Errors =======
        echo "Database error: " . $e->getMessage();
        exit;
    }
} else {
    // ======= Redirect if the Request Method is Not POST =======
    header("Location: ../../register.php");
    exit;
}
?>