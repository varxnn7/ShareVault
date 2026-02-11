<?php
require_once '../includes/db.php';

// files which are not expired 
$stmt = $pdo->query("SELECT original_name, file_key, upload_date FROM files WHERE expiry_date > NOW() ORDER BY upload_date DESC");
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($files);
