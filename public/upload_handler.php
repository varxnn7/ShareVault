<?php
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $accessMode = $_POST['accessMode'] ?? 'public';
    $rawPassword = $_POST['password'] ?? ''; // Get password from JS


    $originalName = basename($file['name']);
    $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
    $fileKey = bin2hex(random_bytes(4));
    $newName = $fileKey . "." . $fileExtension;

    // 7 days from now
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
                // We hash it so even if someone steals the DB, they can't see the password
                $passwordHash = password_hash($rawPassword, PASSWORD_BCRYPT);
            }


            $stmt = $pdo->prepare("INSERT INTO files (file_key, original_name, file_path, access_mode, password, expiry_date) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fileKey, $originalName, $targetFilePath, $accessMode, $passwordHash, $expiryDate]);

            echo json_encode([
                'status' => 'success',
                'link' => "download.php?id=" . $fileKey
            ]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move file to uploads folder.']);
    }
}
 