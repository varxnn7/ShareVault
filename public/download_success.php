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
        <div class="upload-card" style="text-align: center;">
            <div style="margin-bottom: 20px;">
                <i class='bx bxs-check-shield' style="font-size: 5rem; color: var(--success); filter: drop-shadow(0 0 15px rgba(16, 185, 129, 0.4));"></i>
            </div>

            <h1 class="success-title">Success!</h1>
            <p style="color: var(--text-dim); margin-bottom: 30px; line-height: 1.6;">
                Your file has been downloaded successfully.<br>
                Thank you for using <strong>ShareVault</strong>.
            </p>

            <div class="timer-wrapper">
                <i class='bx bx-redo bx-flip-horizontal'></i>
                <span>Returning to home in <span id="countdown">5</span> seconds</span>
            </div>

            <div class="timer-bar-container">
                <div id="timerBar"></div>
            </div>

            <button onclick="window.location.href='index.php'" class="btn-secondary" style="margin-top: 30px;">
                Return Now
            </button>
        </div>
    </div>

    <script>
        /**
         * 1. Speech API - Announce Success
         */
        function speakSuccess(message) {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel(); // Stop any overlapping voices
                const utterThis = new SpeechSynthesisUtterance(message);
                utterThis.pitch = 1.1;
                utterThis.rate = 1.0;
                utterThis.lang = 'en-US';
                window.speechSynthesis.speak(utterThis);
            }
        }

        /**
         * 2. Redirect & Timer Logic
         */
        let timeLeft = 5;
        const countdownEl = document.getElementById('countdown');
        const timerBar = document.getElementById('timerBar');

        const redirectTimer = setInterval(() => {
            timeLeft--;
            countdownEl.textContent = timeLeft;

            // Update progress bar width
            const progress = (timeLeft / 5) * 100;
            timerBar.style.width = progress + "%";

            if (timeLeft <= 0) {
                clearInterval(redirectTimer);
                window.location.href = 'index.php';
            }
        }, 1000);

        /**
         * 3. Initialization
         */
        window.addEventListener('load', () => {
            // Trigger Voice after a short delay so user is settled
            setTimeout(() => {
                speakSuccess("File downloaded successfully. Thank you for using Share Vault.");
            }, 600);
        });
    </script>

</body>

</html>