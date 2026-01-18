<?php
require_once 'config.php';
require_once 'api_response.php';

// ヘッダー設定
header('Content-Type: application/json');

// JSONデータ受信 (ユーザーIDを取得)
$json_data = file_get_contents('php://input');
$request_data = json_decode($json_data, true);

$user_id = $request_data['user_id'] ?? null;

if (empty($user_id)) {
    // 認証情報不足エラー (401 Unauthorized)
    my_log(LOG_ERROR, "User credentials are missing user_id: " . $user_id);
    ApiResponse::sendError(
        ApiResponse::ERROR_AUTH_FAILED, 
        'ユーザー認証情報が不足しています。', 
        ApiResponse::STATUS_CODE_UNAUTHORIZED
    );
    exit; 
}

try {
    $pdo = getDBConnection();

    // 全カラムを取得
    $stmt = $pdo->prepare("
        SELECT 
            id, user_id, title, content, 
            image, latitude, longitude, place, is_public, 
            created_at, updated_at 
        FROM diaries 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$user_id]);
    $diaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Swiftの DiariesDataResponse に合わせ、JSONのルート要素として 'diaries' 配列をラップ
    my_log(LOG_SUCCESS, "Get Diaries Success. diaries: " . json_encode($diaries, JSON_UNESCAPED_UNICODE));
    ApiResponse::sendSuccess(['diaries' => $diaries], ApiResponse::STATUS_CODE_OK);
    exit;

} catch (PDOException $e) {
    my_log(LOG_ERROR, "Get Diaries PDO Error: " . $e->getMessage());
    // データベースエラー (500 Internal Server Error)
    ApiResponse::sendError(
        ApiResponse::ERROR_DB_ERROR, 
        '日誌データの取得中にデータベースエラーが発生しました。', 
        ApiResponse::STATUS_CODE_INTERNAL_SERVER_ERROR
    );
}