<?php
header('Content-Type: application/json; charset=utf-8');

define('DB_HOST', 'localhost'); // データベースのホスト名
define('DB_NAME', 'youichipanda_main'); // データベース名
define('DB_USER', 'youichipanda_ky'); // データベースユーザー名
define('DB_PASS', 'Katana12y'); // データベースパスワード

/**
 * PDOを使ったMySQLデータベース接続を確立する関数。
 * * @return PDO データベース接続オブジェクト
 * @throws Exception 接続に失敗した場合
 */
function getDBConnection(): PDO {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // エラー時に例外を投げる
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // フェッチモードを連想配列に設定
        PDO::ATTR_EMULATE_PREPARES   => false,                  // プリペアドステートメントのエミュレーションを無効化
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (\PDOException $e) {
        // 接続失敗時の詳細情報をログに記録
        error_log("Database Connection Failed: " . $e->getMessage());
        
        // 外部には一般的なエラーメッセージのみを返す（セキュリティのため）
        throw new Exception("データベース接続に失敗しました。サーバー管理者に連絡してください。");
    }
}

// ユーザー登録時のパスワードを安全にハッシュ化する関数
function hashPassword(string $password): string {
    // PASSWORD_DEFAULT (bcrypt) は現在推奨されている強力なハッシュアルゴリズム
    return password_hash($password, PASSWORD_DEFAULT);
}

// ログレベル用の定数
if (!defined('LOG_SUCCESS')) {
    define('LOG_SUCCESS', '[SUCCESS]');
}
if (!defined('LOG_ERROR')) {
    define('LOG_ERROR',   '[ERROR]');
}
if (!defined('LOG_INFO')) {
    define('LOG_INFO',    '[INFO]');
}

/**
 * シンプルなログ出力関数
 */
function my_log($level, $message) {
    // [SUCCESS] メッセージ という形式で出力
    error_log($level . " " . $message);
}