<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$dbname = 'youichipanda_main';  // Laravelの.env に書いてあるDB名
$username = 'youichipanda_ky';
$password = 'Katana12y';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB接続失敗: ' . $e->getMessage()]);
    exit;
}
?>