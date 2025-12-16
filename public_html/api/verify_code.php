<?php

// データベース接続情報ファイル
require_once 'config.php';
require 'api_response.php';

// リクエストボディの解析
$input = json_decode(file_get_contents('php://input'), true);
$mail = $input['mailAddress'] ?? '';
$code = $input['code'] ?? '';

// 入力チェック
if (empty($mail) || empty($code)) {
    ApiResponse::sendError('INVALID_INPUT', 'メールアドレスとコードを入力してください。', 400);
}

try {
    $pdo = getDBConnection();

    // 1. DBから保存されているコードを取得
    // 🚨 テーブル名 mail_auth, 列名 mail_address を使用
    $stmt = $pdo->prepare("SELECT code FROM mail_auth WHERE mail_address = ?");
    $stmt->execute([$mail]);
    $dbCode = $stmt->fetchColumn();

    if (!$dbCode) {
        ApiResponse::sendError('EMAIL_NOT_FOUND', 'コードの要求履歴がありません。', 404);
    }

    // 2. コードの照合
    if ($code === $dbCode) {
        // 3. 認証成功後、セキュリティのためコードをDBから削除
        // 🚨 テーブル名 mail_auth, 列名 mail_address を使用
        $deleteStmt = $pdo->prepare("DELETE FROM mail_auth WHERE mail_address = ?");
        $deleteStmt->execute([$mail]);
        
        ApiResponse::sendSuccess(['mailAddress' => $mail, 'isVerified' => true], 200);

    } else {
        // 4. コード不一致
        ApiResponse::sendError(
            ApiResponse::ERROR_CODE_MISMATCH, 
            '入力されたコードが正しくありません。', 
            ApiResponse::STATUS_CODE_UNAUTHORIZED
        );
    }

} catch (PDOException $e) {
    error_log("Verrify Code Error: " . $e->getMessage()); 
    ApiResponse::sendError(
        ApiResponse::ERROR_UNCATCHABLE, 
        'データベース接続またはクエリ実行エラー', 
        ApiResponse::STATUS_CODE_INTERNAL_SERVER_ERROR
    );
}
exit; 