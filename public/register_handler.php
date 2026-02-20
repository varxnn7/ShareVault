<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        header("Location: register.php?error=empty_fields");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=invalid_email");
        exit;
    }

    try {

        $checkSql = "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$username, $email]);

        if ($checkStmt->fetch()) {

            header("Location: register.php?error=user_exists");
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $insertSql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $insertStmt = $pdo->prepare($insertSql);

        if ($insertStmt->execute([$username, $email, $hashedPassword])) {

            header("Location: login.php?success=account_created");
            exit;
        } else {
            header("Location: register.php?error=server_error");
            exit;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        header("Location: register.php?error=database_error");
        exit;
    }
} else {

    header("Location: register.php");
    exit;
}
