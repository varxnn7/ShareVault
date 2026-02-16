<?php
require_once '../includes/db.php';
// Get the file key from URL
$fileKey = $_GET['id'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM files WHERE file_key = ?");
$stmt->execute([$fileKey]);
$file = $stmt->fetch();

if ($file && file_exists($file['file_path'])) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file['file_path']));
    readfile($file['file_path']);
    exit;
}
