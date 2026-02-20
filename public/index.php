<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FileShare | Secure File Transfer</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

    <div class="container">
        <div class="upload-card">

            <div id="uploadLoader" class="loader-overlay">
                <div class="spinner"></div>
                <div class="loader-text">Encrypting & Uploading...</div>
            </div>

            <header class="main-header">
                <div class="header-left"></div>
                <div class="header-center">
                    <h1>FileShare</h1>
                </div>
                <div class="header-right">
                    <a href="logout.php" class="logout-btn">
                        <i class='bx bx-log-out'></i> Logout
                    </a>
                </div>
            </header>

            <div class="welcome-text">
                <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
                <p>Files expire automatically after 7 days.</p>
            </div>

            <div class="manage-files">
                <button type="button" id="toggleList" class="btn-secondary">
                    <i class='bx bx-list-ul'></i> View My Uploads
                </button>
                <div id="fileList" class="file-list-dropdown" style="display: none;">
                    <p class="loading-text" style="padding:10px;">Loading files...</p>
                </div>
            </div>

            <form id="uploadForm">
                <div class="drop-zone" id="dropZone">
                    <i class='bx bxs-cloud-upload'></i>
                    <p>Drag & drop files or <span>Browse</span></p>
                    <input type="file" id="fileInput" name="file" hidden>
                </div>

                <div class="settings">
                    <div class="setting-item">
                        <label>Access Mode</label>
                        <select id="accessMode">
                            <option value="public">Public (Anyone with link)</option>
                            <option value="restricted">Restricted (Password required)</option>
                        </select>
                    </div>

                    <div class="setting-item" style="position: relative;">
                        <label>Share with (Email)</label>

                        <div id="selectedEmails" class="selected-tags-container"></div>

                        <div class="search-wrapper" style="position: relative;">
                            <input type="text" id="emailSearch" class="password-input" placeholder="Search emails..." autocomplete="off">

                            <div id="emailDropdown" class="email-dropdown-child"></div>
                        </div>
                    </div>

                    <div class="setting-item" id="passwordContainer" style="display: none;">
                        <label>Set Password</label>
                        <input type="password" id="filePassword" class="password-input" placeholder="Create a password...">
                    </div>
                </div>

                <div id="progressContainer" style="display: none; margin: 20px 0;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 5px;">
                        <span>Uploading...</span>
                        <span id="progressPercent">0%</span>
                    </div>
                    <div class="progress-bar-bg" style="width: 100%; height: 8px; background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden;">
                        <div id="progressBarFill" style="width: 0%; height: 100%; background: var(--primary); transition: width 0.1s;"></div>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Generate Secure Link</button>
            </form>

            <div class="result-card" id="resultArea">
                <p>Your file is ready!</p>
                <div class="link-box">
                    <input type="text" id="shareLink" value="#" readonly>
                    <button type="button" onclick="copyLink()" aria-label="Copy link to clipboard">
                        <i class='bx bx-copy'></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js" defer></script>
</body>

</html>