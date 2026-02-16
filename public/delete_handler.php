<?php
session_start();
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if (isset($_GET['id'])) {
    $fileKey = $_GET['id'];
    $userId = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("SELECT file_path FROM files WHERE file_key = ? AND user_id = ?");
        $stmt->execute([$fileKey, $userId]);
        $file = $stmt->fetch();

        if ($file) {
            if (file_exists($file['file_path'])) {
                if (!unlink($file['file_path'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete physical file.']);
                    exit;
                }
            }

            $deleteStmt = $pdo->prepare("DELETE FROM files WHERE file_key = ?");
            $deleteStmt->execute([$fileKey]);

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'File not found or access denied.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file ID provided.']);
}