<?php
require_once 'db.php';

// fetch files that have expire
$stmt = $pdo->prepare("SELECT id, file_path FROM files WHERE expiry_date < NOW()");
$stmt->execute();
$expiredFiles = $stmt->fetchAll();

$count = 0;
foreach ($expiredFiles as $file) {
    // delete the file
    if (file_exists($file['file_path'])) {
        unlink($file['file_path']);
    }

    // delete from database
    $deleteStmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
    $deleteStmt->execute([$file['id']]);
    $count++;
}

echo "Cleanup successful. Removed $count expired files.";