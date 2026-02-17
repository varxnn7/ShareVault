<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

// 1. Authorization Check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if (isset($_GET['id'])) {
    $fileKey = $_GET['id'];
    $userId = $_SESSION['user_id'];

    try {
        // 2. Fetch file to verify ownership and get path
        $stmt = $pdo->prepare("SELECT file_path FROM files WHERE file_key = ? AND user_id = ?");
        $stmt->execute([$fileKey, $userId]);
        $file = $stmt->fetch();

        if ($file) {
            $path = $file['file_path'];

            // 3. Delete physical file if path exists
            if (!empty($path) && file_exists($path)) {
                if (!unlink($path)) {
                    echo json_encode(['status' => 'error', 'message' => 'System could not remove the file from storage.']);
                    exit;
                }
            }

            // 4. Remove database record (added user_id check for extra safety)
            $deleteStmt = $pdo->prepare("DELETE FROM files WHERE file_key = ? AND user_id = ?");
            $deleteStmt->execute([$fileKey, $userId]);

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'File not found or access denied.']);
        }
    } catch (PDOException $e) {
        // Log the error internally and show a clean message to user
        echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file ID provided.']);
}
