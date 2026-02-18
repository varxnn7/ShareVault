<?php
session_start();
require_once '../includes/db.php';

$fileKey = $_GET['id'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM files WHERE file_key = ?");
$stmt->execute([$fileKey]);
$file = $stmt->fetch();

if ($file && file_exists($file['file_path'])) {


    $isRestricted = ($file['access_mode'] === 'restricted');
    $isUnlocked = isset($_SESSION['unlocked_' . $fileKey]) && $_SESSION['unlocked_' . $fileKey] === true;

    if ($isRestricted && !$isUnlocked) {

        die("Access Denied: Please provide the correct password via the download page.");
    }


    if (ob_get_level()) {
        ob_end_clean();
    }


    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file['file_path']));


    readfile($file['file_path']);
    exit;
} else {
    die("File not found.");
}
