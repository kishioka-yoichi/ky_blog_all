<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT id, pass, mail, name, ip, created_at, updated_at FROM accounts");
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($accounts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => '取得失敗: ' . $e->getMessage()]);
}