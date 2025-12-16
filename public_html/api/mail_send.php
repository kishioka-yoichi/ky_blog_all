<?php

// データベース接続情報ファイル
require_once 'config.php';
require 'api_response.php';



function sendVerificationEmail($to, $code) {
    $subject = "OMOIDay仮会員登録のご案内";
    $body = "OMOIDayからメール送信しました。\r\nサイトに戻り以下のパスコードを入力をし、会員登録を完了してください。\r\nパスコード = " . $code;"あなたの確認コードは： " . $code . " です。";
    $headers = "Flom: youichipanda@gmail.com";
    $headers .= "Content-type: text/plain; charset=UTF-8";

    // 実際のメール送信処理
    return mb_send_mail($to, $subject, $body, $headers);
}

// リクエストボディの解析
$input = json_decode(file_get_contents('php://input'), true);
$mail = $input['mailAddress'] ?? ''; // クライアントからは 'mailAddress' として受け取る

if (empty($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    ApiResponse::sendError(
    ApiResponse::ERROR_USER_NOT_FOUND, 
    'メールアドレスが空または不正', 
    ApiResponse::STATUS_CODE_BAD_REQUEST
    );
}

// 1. 4桁のランダムな数字を生成
$code = str_pad(random_int(0,9999),4,0, STR_PAD_LEFT);

try {
    $pdo = getDBConnection();
    
    // 2. メール送信
    if (!sendVerificationEmail($mail, $code)) {
        ApiResponse::sendError(
            ApiResponse::ERROR_MAIL_SEND_FAIL, 
            '認証メールの送信に失敗しました。', 
            ApiResponse::STATUS_CODE_INTERNAL_SERVER_ERROR
        );
    }
    
    // 3. DBにコードを保存
    // テーブル名 mail_auth, 列名 mail_address, code を使用
    $stmt = $pdo->prepare("
        INSERT INTO mail_auth (mail_address, code) 
        VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE code = VALUES(code)
    ");
    $stmt->execute([$mail, $code]);

    ApiResponse::sendSuccess(['isSuccess' => true], ApiResponse::STATUS_CODE_OK);

} catch (PDOException $e) {
    error_log("Mail Send Error: " . $e->getMessage()); 
    ApiResponse::sendError(
        ApiResponse::ERROR_UNCATCHABLE, 
        'データベース接続またはクエリ実行エラー', 
        ApiResponse::STATUS_CODE_INTERNAL_SERVER_ERROR
    );
}

exit; 