<?php
// DB接続情報とレスポンスヘルパー
require_once 'config.php';
require_once 'api_response.php';

// JSONデータ受信
$json_data = file_get_contents('php://input');
$request_data = json_decode($json_data, true);

// ------------------------------------------------------------------
// 1. リクエストデータの取得とバリデーション
// ------------------------------------------------------------------

// Laravelの $request から取得するデータに相当するものをJSONから取得
$id = $request_data['id'] ?? '';
$pass = $request_data['pass'] ?? '';
$mail = $request_data['mail'] ?? '';
$name = $request_data['name'] ?? '';

// IPアドレスはサーバー側で取得
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'; 

// 必須項目のチェック
if (empty($id) || empty($pass) || empty($mail) || empty($name)) {
    ApiResponse::sendError(
        ApiResponse::ERROR_INVALID_INPUT, 
        '必須項目（ID, パスワード, メールアドレス, 名前）が不足しています。', 
        ApiResponse::STATUS_CODE_BAD_REQUEST
    );
}

// ------------------------------------------------------------------
// 2. データベース操作
// ------------------------------------------------------------------

try {
    $pdo = getDBConnection();

    // Laravel Eloquentの create() メソッドに相当する処理をPDOで実行
    $stmt = $pdo->prepare("
        INSERT INTO accounts (id, pass, mail, name, ip, created_at, updated_at) 
        VALUES (:id, :pass, :mail, :name, :ip, NOW(), NOW())
    ");
    
    // バインドパラメータの設定
    $stmt->bindValue(':id', $id);
    $stmt->bindValue(':pass', $pass); // ハッシュ値を挿入
    $stmt->bindValue(':mail', $mail);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':ip', $ip);
    
    $stmt->execute();
    
    // 💡 成功レスポンス（Laravelの redirect('/main') に相当する結果）
    // ユーザーIDは文字列型なので、文字列で返す
    ApiResponse::sendSuccess([
        'id' => $id,
        'isSuccess' => true
    ], ApiResponse::STATUS_CODE_CREATED);

} catch (PDOException $e) {
    // IDまたはメールアドレスの重複エラー (409 Conflict)
    if ($e->getCode() === '23000') {
        ApiResponse::sendError(
            ApiResponse::ERROR_CONFLICT, 
            'そのIDまたはメールアドレスは既に登録されています。', 
            ApiResponse::STATUS_CODE_CONFLICT // 409 Conflict
        );
    }
    
    error_log("Account Creation PDO Error: " . $e->getMessage());
    ApiResponse::sendError(
        ApiResponse::ERROR_DB_ERROR, 
        'データベースエラーによりアカウント作成に失敗しました。', 
        ApiResponse::STATUS_CODE_INTERNAL_SERVER_ERROR
    );
}
exit; 