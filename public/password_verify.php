<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locked File | FileShare</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Restricted Access</h1>
            <p>This file is password protected.</p>
        </header>

        <div class="upload-card">
            <form method="POST">
                <div class="setting-item">
                    <label for="entered_pass">Enter Password</label>
                    <input type="password" name="entered_pass" id="entered_pass" class="password-input" required>
                </div>

                <?php if (isset($error)): ?>
                    <p id="errorMessage" class="error-text">
                        <?php echo $error; ?>
                    </p>
                <?php endif; ?>

                <button type="submit" class="btn-primary">Unlock & Download</button>
            </form>
        </div>
    </div>

    <script src="../assets/js/script.js" defer></script>
</body>

</html>