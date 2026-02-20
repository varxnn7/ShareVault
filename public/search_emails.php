<?php
session_start();
require_once '../includes/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$query = $_GET['query'] ?? '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {

    $stmt = $pdo->prepare("SELECT email FROM users WHERE email LIKE ? AND id != ? LIMIT 5");
    $stmt->execute(["%$query%", $_SESSION['user_id']]);
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($emails);
} catch (PDOException $e) {
    echo json_encode([]);
}
