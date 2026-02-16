<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Success | ShareVault</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="container">
        <div class="upload-card success-card">
            <i class='bx bxs-check-circle success-icon'></i>

            <h1 class="success-title">File Ready!</h1>

            <p class="success-text">
                Your download has started.<br>
                Thank you for choosing <strong>ShareVault</strong>.
            </p>
            <p style="font-size: 0.75rem; color: #fbbf24; margin-bottom: 10px;">
                <i class='bx bx-time-five'></i> Note: This link expires in 7 days.
            </p>

            <div class="action-group">
                <a href="index.php" class="btn-primary btn-home">
                    <i class='bx bx-home-alt'></i> Return Now
                </a>
            </div>

            <div class="timer-wrapper">
                <span>Redirecting to dashboard in</span>
                <span id="countdown">5</span>s
            </div>
            <div class="timer-bar-container">
                <div id="timerBar"></div>
            </div>
        </div>
    </div>

    <script>
        let timeLeft = 5;
        const countdownEl = document.getElementById('countdown');
        const timerBar = document.getElementById('timerBar');

        const timer = setInterval(() => {
            timeLeft--;
            countdownEl.innerText = timeLeft;

            // Update the progress bar width
            const percentage = (timeLeft / 5) * 100;
            timerBar.style.width = percentage + "%";

            if (timeLeft <= 0) {
                clearInterval(timer);
                window.location.href = "index.php";
            }
        }, 1000);
    </script>
</body>

</html>