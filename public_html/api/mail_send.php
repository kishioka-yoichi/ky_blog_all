<?php

// データベース接続情報ファイル
require_once 'config.php';
require 'api_response.php';

mb_language("Japanese");
mb_internal_encoding("UTF-8");

function sendVerificationEmail($to, $code) {

    $subject = "OMOIDay仮会員登録のご案内";
    $message = "OMOIDayからメール送信しました。\r\nサイトに戻り以下のパスコードを入力をし、会員登録を完了してください。\r\nパスコード = " . $code;
    
    // 1. Laravel側の書き方に合わせ、余計なContent-type等を一度外してテスト
    // 2. Fromのアドレスをドメイン一致に変更（Xserverの推奨）
    $from = "youichipanda@ky-blog.com"; 
    $headers = "From: " . $from;

    my_log(LOG_INFO, "Mail Send Executed for: " . $to);
    
    // 3. 第5引数で送信元をOSレベルで明示（これが速度と到達率に効きます）
    return mb_send_mail($to, $subject, $message, $headers, "-f " . $from);
}

// リクエストボディの解析
$input = json_decode(file_get_contents('php://input'), true);
$mail = $input['mail_address'] ?? '';

if (empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    my_log(LOG_ERROR, "Empty or invalid email address");
    ApiResponse::sendError(
    ApiResponse::ERROR_USER_NOT_FOUND, 
    'メールアドレスが空または不正', 
    ApiResponse::STATUS_CODE_BAD_REQUEST
    );
    exit; 
}

// 1. 4桁のランダムな数字を生成
$code = str_pad(random_int(0,9999),4,0, STR_PAD_LEFT);

try {
    $pdo = getDBConnection();
    
    // 2. メール送信
    if (!sendVerificationEmail($mail, $code)) {
        my_log(LOG_ERROR, "Mail Send Failure");
        ApiResponse::sendError(
            ApiResponse::ERROR_MAIL_SEND_FAIL, 
            '認証メールの送信に失敗しました。', 
            ApiResponse::STATUS_CODE_INTERNAL_SERVER_ERROR
        );
        exit;
    }
    
    // 3. DBにコードを保存
    // テーブル名 mail_auth, 列名 mail_address, code を使用
    $stmt = $pdo->prepare("
        INSERT INTO mail_auth (mail_address, code) 
        VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE code = VALUES(code)
    ");
    $stmt->execute([$mail, $code]);

    my_log(LOG_SUCCESS, "Mail Send Success");
    ApiResponse::sendSuccess(['is_success' => true], ApiResponse::STATUS_CODE_OK);

} catch (PDOException $e) {
    my_log(LOG_ERROR, "Mail Send Error: " . $e->getMessage());
    ApiResponse::sendError(
        ApiResponse::ERROR_UNCATCHABLE, 
        'データベース接続またはクエリ実行エラー', 
        ApiResponse::STATUS_CODE_INTERNAL_SERVER_ERROR
    );
}

exit; 