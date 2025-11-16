<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * データベースの 'accounts' テーブルに対応するモデル
 */
class Account extends Model
{
    use HasFactory;

    // 1. モデルが対応するテーブル名を指定（Laravelの命名規則から外れる場合）
    // 通常はクラス名(Account)から自動的に 'accounts' が推測されますが、明確にしておくと安全です。
    protected $table = 'accounts';

    // 2. create()やupdate()で一括代入を許可するカラムを指定 (マスアサインメント対策)
    // ここに指定したカラムのみが、Account::create($param) で挿入可能になります。
    protected $fillable = [
        'id',
        'pass',
        'mail',
        'name',
        'ip',
    ];

    // 3. プライマリキーの型を指定
    // 'id' カラムが文字列（VARCHARなど）の場合に必要です。
    protected $keyType = 'string';

    // 4. プライマリキーが自動増分(Auto Increment)ではないことを指定
    // 'id' をユーザーが入力する値として使うため、自動増分を無効にします。
    public $incrementing = false;
}