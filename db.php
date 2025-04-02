<?php
$host = '127.0.0.1';
$port = '3306';
$dbname = 'summitgu_capitalcoin';
$username = 'summitgu_capitalcoin';
$password = 'capitalcoin';

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}