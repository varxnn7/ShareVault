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
    <title>Join ShareVault</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Create Account</h1>
            <p>Join the secure vault community.</p>
        </header>
        <div class="upload-card">
            <form method="POST">
                <div class="setting-item">
                    <label>Username</label>
                    <input type="text" name="username" class="password-input" required>
                </div>
                <div class="setting-item">
                    <label>Password</label>
                    <input type="password" name="password" class="password-input" required>
                </div>
                <?php if ($message): ?> <p class="error-text"><?php echo $message; ?></p> <?php endif; ?>
                <button type="submit" class="btn-primary">Sign Up</button>
            </form>
            <p style="text-align:center; margin-top:15px; font-size:0.8rem;">
                Already have an account? <a href="login.php" style="color:var(--primary);">Login here</a>
            </p>
        </div>
    </div>
</body>

</html>