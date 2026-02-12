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
        <header>
            <h1>FileShare</h1>
            <p>Files expire automatically after <strong>7 days</strong>.</p>
        </header>
        <div class="manage-files">
            <button type="button" id="toggleList" class="btn-secondary">
                <i class='bx bx-list-ul'></i> View My Uploads
            </button>
            <div id="fileList" class="file-list-dropdown" style="display: none;">
                <p class="loading-text">Loading files...</p>
            </div>
        </div>

        <form id="uploadForm" class="upload-card">
            <div class="drop-zone" id="dropZone">
                <i class='bx bxs-cloud-upload'></i>
                <p>Drag & drop files or <span>Browse</span></p>
                <input type="file" id="fileInput" name="file" hidden>
            </div>

            <div class="settings">
                <div class="setting-item">
                    <label for="accessMode">Access Control</label>
                    <select id="accessMode" name="accessMode">
                        <option value="public">🔓 Public (Anyone with link)</option>
                        <option value="restricted">🔒 Restricted (Password required)</option>
                    </select>
                </div>
                <div class="setting-item" id="passwordContainer" style="display: none; margin-top: 10px;">
                    <label for="filePassword">Set Password</label>
                    <input type="password" id="filePassword" name="password" placeholder="Enter secure password"
                        style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: #1e293b; color: white; outline: none;">
                </div>
            </div>

            <button type="submit" class="btn-primary">Generate Secure Link</button>
        </form>

        <div class="result-card" id="resultArea">
            <p>Your file is ready!</p>
            <div class="link-box">
                <input type="text" id="shareLink" value="#" readonly>
                <button onclick="copyLink()"><i class='bx bx-copy'></i></button>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js" defer></script>
</body>

</html>