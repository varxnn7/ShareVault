<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../includes/db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        session_write_close();
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | ShareVault</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="auth-page">
    <div class="container">
        <div class="upload-card auth-card">
            <div class="auth-header">
                <i class='bx bxs-lock-open' style="font-size: 3rem; color: var(--primary);"></i>
                <h2>Welcome Back</h2>
                <p>Securely access your vault</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-msg" style="color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-size: 0.9rem;">
                    Invalid username/email or password.
                </div>
            <?php endif; ?>

            <form action="login_handler.php" method="POST">
                <div class="setting-item">
                    <label>Username or Email</label>
                    <div class="search-wrapper">
                        <input type="text" name="login_identity" class="password-input" placeholder="Enter your credentials" required>
                    </div>
                </div>

                <div class="setting-item">
                    <label>Password</label>
                    <div class="search-wrapper">
                        <input type="password" name="password" class="password-input" placeholder="Enter password" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%; margin-top: 20px;">
                    <i class='bx bx-log-in-circle'></i> Login
                </button>
            </form>

            <div class="auth-footer">
                <p>New here? <a href="register.php">Create an account</a></p>
            </div>
        </div>
    </div>
</body>

</html>