<?php
// ⚠️ 本番環境では、デバッグ目的以外のPHPエラー表示は必ずOFFにしてください。
// エラーが出力されるとJSONが壊れ、iOS側でデコーディングエラーが発生します。
// ini_set('display_errors', 0); 
// error_reporting(0);

// データベース接続情報ファイル
require_once 'config.php';
require 'api_response.php';

// ヘッダー設定
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // 開発環境向け

// JSONデータ受信
$json_data = file_get_contents('php://input');
$request_data = json_decode($json_data, true);

// 変数の取得とNULLチェック
$user_id = $request_data['id'] ?? '';
$pass = $request_data['password'] ?? '';

// 入力値の基本チェック
if (empty($user_id) || empty($pass)) {
    my_log(LOG_ERROR, "Input value is empty: user_id = " . $user_id . ", pass" . $pass);
    ApiResponse::sendError(
    ApiResponse::ERROR_USER_NOT_FOUND, 
    'ユーザーIDまたはパスワードが空', 
    ApiResponse::STATUS_CODE_BAD_REQUEST
    );
    exit; 
}

try {
    // データベース接続
    $pdo = getDBConnection();

    // 1. ユーザーIDでレコードを取得
    // パスワード照合のためにハッシュ化されたパスワードを取得
    $stmt = $pdo->prepare("SELECT id, pass FROM accounts WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // 2. 受信パスワードとDBのハッシュ値を照合
        // password_verify() は安全にハッシュを検証するPHP標準関数
        // if (password_verify($pass, $user['pass'])) {
        if ($pass == $user['pass']) {
            // 
            my_log(LOG_SUCCESS, "Login Success");
            ApiResponse::sendSuccess(['id' => $user['id']], ApiResponse::STATUS_CODE_OK);
        } else {
            // パスワード不一致
            my_log(LOG_ERROR, "Password mismatch");
            ApiResponse::sendError(
                ApiResponse::ERROR_AUTH_FAILED, 
                'パスワード不一致', 
                ApiResponse::STATUS_CODE_UNAUTHORIZED
            );
        }
    } else {
        // ユーザーIDが見つからない
        my_log(LOG_ERROR, "User ID not found");
        ApiResponse::sendError(
            ApiResponse::ERROR_USER_NOT_FOUND, 
            'ユーザーIDが見つからない', 
            ApiResponse::STATUS_CODE_UNAUTHORIZED
        );
    }

} catch (PDOException $e) {
    // データベース接続またはクエリ実行エラー
    // エラーログに出力し、ユーザーには一般的なメッセージを返す
    my_log(LOG_ERROR, "Login PDO Error: " . $e->getMessage());
    ApiResponse::sendError(
        ApiResponse::ERROR_DB_ERROR, 
        'データベース接続またはクエリ実行エラー', 
        ApiResponse::STATUS_CODE_INTERNAL_SERVER_ERROR
    );
} catch (Exception $e) {
    // その他の予期せぬエラー
    my_log(LOG_ERROR, "Login General Error: " . $e->getMessage());
    ApiResponse::sendError(
        ApiResponse::ERROR_UNCATCHABLE, 
        'データベース接続またはクエリ実行エラー', 
        ApiResponse::STATUS_CODE_INTERNAL_SERVER_ERROR
    );
}

// 処理を確実に終了させる
exit; 