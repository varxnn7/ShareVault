<?php
session_start(); // Necessary to check if the user unlocked the file
require_once '../includes/db.php';

$fileKey = $_GET['id'] ?? '';

// 1. Fetch file details
$stmt = $pdo->prepare("SELECT * FROM files WHERE file_key = ?");
$stmt->execute([$fileKey]);
$file = $stmt->fetch();

if ($file && file_exists($file['file_path'])) {

    // 2. Security Check: Is it public or has the session unlocked this specific key?
    $isRestricted = ($file['access_mode'] === 'restricted');
    $isUnlocked = isset($_SESSION['unlocked_' . $fileKey]) && $_SESSION['unlocked_' . $fileKey] === true;

    if ($isRestricted && !$isUnlocked) {
        // Stop unauthorized direct access
        die("Access Denied: Please provide the correct password via the download page.");
    }

    // 3. Clean the output buffer to prevent file corruption
    if (ob_get_level()) {
        ob_end_clean();
    }

    // 4. Force the browser to download the file
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file['file_path']));

    // 5. Stream the file
    readfile($file['file_path']);
    exit;
} else {
    die("File not found.");
}
