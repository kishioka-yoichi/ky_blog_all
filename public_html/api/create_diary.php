<?php
require_once 'config.php';
require_once 'api_response.php';

header('Content-Type: application/json');

$json_data = file_get_contents('php://input');
$request_data = json_decode($json_data, true);

// Swift側から送られてくる全項目を取得
$user_id    = $request_data['user_id'] ?? null;
$title      = $request_data['title'] ?? '';
$content    = $request_data['content'] ?? '';
$image      = $request_data['image'] ?? null;
$latitude   = $request_data['latitude'] ?? null;
$longitude  = $request_data['longitude'] ?? null;
$place      = $request_data['place'] ?? null;
$is_public  = $request_data['is_public'] ?? 1;
// Swift側で生成された日時を受け取る
$created_at = $request_data['created_at'] ?? null;
$updated_at = $request_data['updated_at'] ?? null;

if (empty($user_id)) {
    ApiResponse::sendError(ApiResponse::ERROR_AUTH_FAILED, 'ユーザーIDが不足しています。', 401);
    exit;
}

try {
    $pdo = getDBConnection();

    // INSERT文：NOW()を使わず、Swiftから送られた ? を使用する
    $stmt = $pdo->prepare("
        INSERT INTO diaries (
            user_id, title, content, image, 
            latitude, longitude, place, is_public, 
            created_at, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $user_id, $title, $content, $image,
        $latitude, $longitude, $place, $is_public,
        $created_at, $updated_at
    ]);

    // 保存したレコードのIDを取得
    $new_id = $pdo->lastInsertId();

    // Swift側に保存成功したデータ（ID含む）を返す
    $response_data = [
        'id'          => (int)$new_id,
        'user_id'     => $user_id,
        'title'       => $title,
        'content'     => $content,
        'image'       => $image,
        'latitude'    => $latitude !== null ? (float)$latitude : null,
        'longitude'   => $longitude !== null ? (float)$longitude : null,
        'place'       => $place,
        'is_public'   => (int)$is_public,
        'created_at'  => $created_at,
        'updated_at'  => $updated_at
    ];

    my_log(LOG_SUCCESS, "Diary Created: ID " . $new_id);
    ApiResponse::sendSuccess([
        'is_success' => true,
        'diary_id' => (int)$new_id
    ], ApiResponse::STATUS_CODE_OK);

} catch (PDOException $e) {
    my_log(LOG_ERROR, "Create Diary Error: " . $e->getMessage());
    ApiResponse::sendError(ApiResponse::ERROR_DB_ERROR, 'DBエラーが発生しました。', 500);
}