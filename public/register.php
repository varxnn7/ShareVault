<?php
require_once '../includes/db.php';
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashedPassword]);
            header("Location: login.php?registered=true");
            exit;
        } catch (PDOException $e) {
            $message = ($e->getCode() == 23000) ? "Username already exists!" : "Registration failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | ShareVault</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="auth-page">
    <div class="container">
        <div class="upload-card auth-card">
            <div class="auth-header">
                <i class='bx bxs-cloud-upload' style="font-size: 3rem; color: var(--primary);"></i>
                <h2>Create Account</h2>
                <p>Join ShareVault for secure file sharing</p>
            </div>

            <form action="register_handler.php" method="POST">
                <div class="setting-item">
                    <label>Username</label>
                    <div class="search-wrapper">
                        <input type="text" name="username" class="password-input" placeholder="Choose a username" required>
                    </div>
                </div>

                <div class="setting-item">
                    <label>Email Address</label>
                    <div class="search-wrapper">
                        <input type="email" name="email" class="password-input" placeholder="Enter your email" required>
                    </div>
                </div>

                <div class="setting-item">
                    <label>Password</label>
                    <div class="search-wrapper">
                        <input type="password" name="password" class="password-input" placeholder="Create a password" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 20px;">
                    <i class='bx bx-user-plus'></i> Register
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login Now</a></p>
            </div>
        </div>
    </div>
</body>

</html>