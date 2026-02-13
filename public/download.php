<?php
require_once '../includes/db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access Denied. Please login to download files.");
}
$fileKey = $_GET['id'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM files WHERE file_key = ?");
$stmt->execute([$fileKey]);
$file = $stmt->fetch();

if (!$file) die("File not found or link expired.");

// 7 days 
if (new DateTime() > new DateTime($file['expiry_date'])) die("This link has expired.");

// if ($file['access_mode'] === 'restricted') {
//     if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entered_pass'])) {
//         if (password_verify($_POST['entered_pass'], $file['password'])) {
//             serveFile($file);
//         } else {
//             $error = "Incorrect Password!";
//         }
//         if (password_verify($_POST['entered_pass'], $file['password'])) {
//             serveFile($file);
//         } else {
//             $error = "Incorrect Password!";
//         }
//     }
//     include 'password_verify.php';
//     exit;
// }
if ($file['access_mode'] === 'restricted') {
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entered_pass'])) {
        if (password_verify($_POST['entered_pass'], $file['password'])) {
            serveFile($file);
        } else {
            $error = "Incorrect Password!";
        }
    }
    include 'password_verify.php';
    exit;
}


serveFile($file);

function serveFile($file)
{
    if (file_exists($file['file_path'])) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
        readfile($file['file_path']);
        exit;
    }
}
