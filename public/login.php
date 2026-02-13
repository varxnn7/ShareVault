<?php
// 1. Logic MUST come before ANY HTML
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
        // This will now work because no HTML has been sent yet
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
    <title>Login | ShareVault</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Welcome Back</h1>
            <p>Login to your secure vault.</p>
        </header>

        <div class="upload-card">
            <form method="POST">
                <div class="setting-item">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Username" class="password-input" required autofocus>
                </div>
                <div class="setting-item">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Password" class="password-input" required>
                </div>

                <?php if ($error): ?>
                    <p class="error-text" style="color: #ef4444; font-size: 0.8rem; margin-bottom: 15px;">
                        <?php echo $error; ?>
                    </p>
                <?php endif; ?>

                <?php if (isset($_GET['registered'])): ?>
                    <p style="color: #10b981; font-size: 0.8rem; margin-bottom: 15px;">
                        Registration successful! Please login.
                    </p>
                <?php endif; ?>

                <button type="submit" class="btn-primary">Login</button>
            </form>

            <p style="text-align:center; margin-top:15px; font-size:0.8rem;">
                New here? <a href="register.php" style="color:var(--primary); text-decoration: none;">Create an account</a>
            </p>
        </div>
    </div>
</body>

</html>