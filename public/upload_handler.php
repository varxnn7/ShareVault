<?php
ob_start();
session_start();
require_once 'mail.php'; // Direct link because both are in the public folder
require_once '../includes/db.php';

ob_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $userId = $_SESSION['user_id'];
    $accessMode = $_POST['accessMode'] ?? 'public';
    $rawPassword = $_POST['password'] ?? '';
    $sharedEmailsRaw = $_POST['sharedEmails'] ?? '[]';
    $emailsToNotify = json_decode($sharedEmailsRaw, true);

    $originalName = basename($file['name']);
    $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
    $fileKey = bin2hex(random_bytes(4));
    $newName = $fileKey . "." . $fileExtension;
    $expiryDate = date('Y-m-d H:i:s', strtotime('+7 days'));


    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $fullURL = $protocol . $_SERVER['HTTP_HOST'] . str_replace('upload_handler.php', 'download.php?id=' . $fileKey, $_SERVER['PHP_SELF']);

    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $targetFilePath = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        try {
            $passwordHash = ($accessMode === 'restricted' && !empty($rawPassword))
                ? password_hash($rawPassword, PASSWORD_BCRYPT) : null;


            $sql = "INSERT INTO files (file_key, original_name, file_path, access_mode, password, expiry_date, user_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$fileKey, $originalName, $targetFilePath, $accessMode, $passwordHash, $expiryDate, $userId]);


            $verifiedCount = 0;
            if (is_array($emailsToNotify)) {
                foreach ($emailsToNotify as $email) {
                    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                    $check->execute([$email]);

                    if ($check->fetch()) {
                        sendShareEmail($email, $fullURL, $originalName);
                        $verifiedCount++;
                    }
                }
            }

            echo json_encode([
                'status' => 'success',
                'link' => "download.php?id=" . $fileKey,
                'message' => "Uploaded! Shared with $verifiedCount registered users."
            ]);
        } catch (PDOException $e) {
            if (file_exists($targetFilePath)) unlink($targetFilePath);
            echo json_encode(['status' => 'error', 'message' => 'Database Error.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Upload Failed.']);
    }
}
ob_end_flush();
