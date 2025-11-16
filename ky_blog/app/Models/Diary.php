<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diary extends Model {
    use HasFactory;

    // ユーザーが入力する可能性のあるフィールドを定義
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'image',
        'latitude',
        'longitude',
        'place',
        'is_public',
    ];
    // 日付をDateTime/Carbonオブジェクトにキャストする設定を追加
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /**
     * この日記を投稿したユーザー（Account）を取得する
     */
    public function account()
    {
        // 外部キー: 'user_id' (diariesテーブル), ローカルキー: 'id' (accountsテーブル)
        return $this->belongsTo(Account::class, 'user_id', 'id');
    }
}