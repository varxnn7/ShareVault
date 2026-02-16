<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please login first.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $userId = $_SESSION['user_id'];
    $accessMode = $_POST['accessMode'] ?? 'public';
    $rawPassword = $_POST['password'] ?? '';

    $originalName = basename($file['name']);
    $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);

    $fileKey = bin2hex(random_bytes(4));
    $newName = $fileKey . "." . $fileExtension;

    $expiryDate = date('Y-m-d H:i:s', strtotime('+7 days'));

    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $targetFilePath = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        try {
            $passwordHash = null;
            if ($accessMode === 'restricted' && !empty($rawPassword)) {
                $passwordHash = password_hash($rawPassword, PASSWORD_BCRYPT);
            }

            $sql = "INSERT INTO files (file_key, original_name, file_path, access_mode, password, expiry_date, user_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $fileKey,
                $originalName,
                $targetFilePath,
                $accessMode,
                $passwordHash,
                $expiryDate,
                $userId
            ]);

            echo json_encode([
                'status' => 'success',
                'link' => "download.php?id=" . $fileKey
            ]);
        } catch (PDOException $e) {
            if (file_exists($targetFilePath)) unlink($targetFilePath);
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Upload failed. Check folder permissions.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
