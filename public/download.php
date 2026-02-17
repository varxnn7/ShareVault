<?php
session_start();
require_once '../includes/db.php';

$fileKey = $_GET['id'] ?? '';

// 1. Fetch file details
$stmt = $pdo->prepare("SELECT * FROM files WHERE file_key = ?");
$stmt->execute([$fileKey]);
$file = $stmt->fetch();

// 2. Redirect if not found
if (!$file) {
    header("Location: index.php");
    exit;
}

$isRestricted = ($file['access_mode'] === 'restricted');
$error = "";

// 3. Password Verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if (password_verify($_POST['password'], $file['password'])) {
        $_SESSION['unlocked_' . $fileKey] = true;
    } else {
        $error = "Incorrect password. Please try again.";
    }
}

$isUnlocked = !$isRestricted || (isset($_SESSION['unlocked_' . $fileKey]) && $_SESSION['unlocked_' . $fileKey] === true);

if (!$isUnlocked): 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access | ShareVault</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container">
        <div class="main-header">
            <div class="header-center"><h1>ShareVault</h1></div>
        </div>
        
        <div class="upload-card">
            <div style="text-align: center; margin-bottom: 24px;">
                <i class='bx bxs-lock-alt' style="font-size: 3.5rem; color: var(--primary); margin-bottom:15px;"></i>
                <h2>Password Required</h2>
                <p style="color: var(--text-dim); font-size: 0.9rem;">
                    Enter the password to download:<br>
                    <strong style="color: var(--text-main);"><?php echo htmlspecialchars($file['original_name']); ?></strong>
                </p>
            </div>

            <form method="POST">
                <div class="setting-item">
                    <input type="password" name="password" class="password-input" placeholder="Enter password..." required autofocus>
                </div>
                <?php if($error): ?>
                    <p style="color: #ef4444; font-size: 0.85rem; text-align: center; margin-top: -10px; margin-bottom: 15px;">
                        <i class='bx bx-error-circle'></i> <?php echo $error; ?>
                    </p>
                <?php endif; ?>
                <button type="submit" class="btn-primary">Unlock File</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php else: ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container" style="text-align: center;">
        <div class="upload-card">
            <i class='bx bx-loader-alt bx-spin' style="font-size: 3.5rem; color: var(--primary); margin-bottom: 20px; display: inline-block;"></i>
            <h2>Preparing Download</h2>
            <p style="color: var(--text-dim); line-height: 1.6;">Your secure link is being verified.<br>Starting your download now...</p>
        </div>
    </div>

    <script>
        // Start download stream
        window.location.href = "process_stream.php?id=<?php echo $fileKey; ?>";
        
        // Wait 2 seconds, then show success page
        setTimeout(() => {
            window.location.href = "download_success.php";
        }, 2000);
    </script>
</body>
</html>
<?php endif; ?>