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
    $raw_diaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $diaries = [];
    foreach ($raw_diaries as $row) {
        $diaries[] = [
            'id'         => (int)$row['id'],
            'user_id'    => $row['user_id'],
            'title'      => $row['title'],
            'content'    => $row['content'],
            'image'      => $row['image'],
            'latitude'   => $row['latitude'] !== null ? (float)$row['latitude'] : null,
            'longitude'  => $row['longitude'] !== null ? (float)$row['longitude'] : null,
            'place'      => $row['place'],
            'is_public'  => (int)$row['is_public'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at']
        ];
    }

    my_log(LOG_SUCCESS, "Get Diaries Success. count: " . count($diaries));
    ApiResponse::sendSuccess(['diaries' => $diaries], ApiResponse::STATUS_CODE_OK);
    exit;

} catch (PDOException $e) {
    my_log(LOG_ERROR, "Get Diaries PDO Error: " . $e->getMessage());
    ApiResponse::sendError(
        ApiResponse::ERROR_DB_ERROR, 
        '日誌データの取得中にデータベースエラーが発生しました。', 
        ApiResponse::STATUS_CODE_INTERNAL_SERVER_ERROR
    );
}