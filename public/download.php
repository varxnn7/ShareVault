<?php
session_start();
require_once '../includes/db.php';

$fileKey = $_GET['id'] ?? '';

// 1. Fetch file details from database
$stmt = $pdo->prepare("SELECT * FROM files WHERE file_key = ?");
$stmt->execute([$fileKey]);
$file = $stmt->fetch();

// 2. If file doesn't exist, redirect home
if (!$file) {
    header("Location: index.php");
    exit;
}

$isRestricted = ($file['access_mode'] === 'restricted');
$error = "";

// 3. Handle Password Verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if (password_verify($_POST['password'], $file['password'])) {
        $_SESSION['unlocked_' . $fileKey] = true;
    } else {
        $error = "Incorrect password. Please try again.";
    }
}

// 4. Check if we should show the Password Form or the Download Bridge
$isUnlocked = !$isRestricted || (isset($_SESSION['unlocked_' . $fileKey]) && $_SESSION['unlocked_' . $fileKey] === true);

if (!$isUnlocked): 
    // SHOW PASSWORD UI
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Download | ShareVault</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="container">
        <div class="main-header">
            <div class="header-left"></div>
            <div class="header-center"><h1>ShareVault</h1></div>
            <div class="header-right"></div>
        </div>
        
        <div class="upload-card">
            <div style="text-align: center; margin-bottom: 20px;">
                <i class='bx bxs-lock-alt' style="font-size: 3rem; color: var(--primary);"></i>
                <h2>Password Protected</h2>
                <p style="color: var(--text-dim);">Enter the password to access <strong><?php echo htmlspecialchars($file['original_name']); ?></strong></p>
            </div>

            <form method="POST">
                <div class="setting-item">
                    <input type="password" name="password" class="password-input" placeholder="Enter password..." required autofocus>
                </div>
                <?php if($error): ?>
                    <p class="error-text" style="text-align:center;"><?php echo $error; ?></p>
                <?php endif; ?>
                <button type="submit" class="btn-primary">Unlock & Download</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php else: 
    // SHOW DOWNLOAD BRIDGE (Triggers download + Redirects to Success)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container" style="text-align: center;">
        <div class="upload-card">
            <i class='bx bx-loader-alt bx-spin' style="font-size: 3rem; color: var(--primary); margin-bottom: 15px;"></i>
            <h2>Preparing Download...</h2>
            <p style="color: var(--text-dim);">Please wait while we secure your file connection.</p>
        </div>
    </div>

    <script>
        // 1. Trigger the actual file stream using the streamer file we created
        window.location.href = "process_stream.php?id=<?php echo $fileKey; ?>";
        
        // 2. Redirect the UI to the Success Page after a short delay
        setTimeout(() => {
            window.location.href = "download_success.php";
        }, 2000);
    </script>
</body>
</html>
<?php endif; ?>