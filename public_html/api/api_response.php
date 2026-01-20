<?php

class ApiResponse {
    // -----------------------------------------
    // 1. 定数定義
    // -----------------------------------------
    
    public const KEY_MESSAGE = 'message';
    public const KEY_ERROR_CODE = 'errorCode';

    // カスタムエラーコード (すべて大文字)
    public const ERROR_INVALID_INPUT = 'INVALID_INPUT'; // 無効な入力値
    public const ERROR_AUTH_FAILED = 'AUTH_FAILED'; // 認証失敗
    public const ERROR_CONFLICT = 'CONFLICT'; // 値の重複
    public const ERROR_DB_ERROR = 'DB_ERROR'; // DBエラー
    public const ERROR_USER_NOT_FOUND = 'USER_NOT_FOUND'; // ユーザーが見つからなかった
    public const ERROR_INVALID_CODE = 'INVALID_CODE'; // 無効なエラーコード
    public const ERROR_MAIL_SEND_FAIL = 'MAIL_SEND_FAIL'; // メール送信失敗
    public const ERROR_CODE_MISMATCH = 'CODE_MISMATCH'; // 認証コードの不一致
    public const ERROR_ARG_EMPTY = 'ARG_EMPTY'; // 引数が空
    public const ERROR_UNCATCHABLE = 'UNCATCHABLE'; // どれにも該当せず、補足できないエラー

    
    // HTTPステータスコード (すべて大文字の定数 - STATUS_CODE_XXX 形式)
    public const STATUS_CODE_OK = 200;
    public const STATUS_CODE_CREATED = 201;
    public const STATUS_CODE_ACCEPTED = 202; // 2FA要求用
    public const STATUS_CODE_BAD_REQUEST = 400;
    public const STATUS_CODE_UNAUTHORIZED = 401;
    public const STATUS_CODE_CONFLICT = 409;
    public const STATUS_CODE_INTERNAL_SERVER_ERROR = 500;

    // -----------------------------------------
    // 2. ヘルパーメソッド
    // -----------------------------------------

    /**
     * 成功時のレスポンス
     * @param array $data レスポンスボディ
     * @param int $statusCode HTTPステータスコード
     */
    public static function sendSuccess(array $data, int $statusCode = self::STATUS_CODE_OK): void {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * エラー時のレスポンス (固定構造)
     * @param string $errorCode エラーコード
     * @param string $message エラーメッセージ
     * @param int $statusCode HTTPステータスコード
     */
    public static function sendError(string $errorCode, string $message, int $statusCode): void {
        http_response_code($statusCode);
        
        $errorBody = [
            self::KEY_ERROR_CODE => $errorCode,
            self::KEY_MESSAGE => $message
        ];
        
        echo json_encode($errorBody, JSON_UNESCAPED_UNICODE);
        exit;
    }
}