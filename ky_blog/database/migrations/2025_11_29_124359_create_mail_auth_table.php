<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * mail_auth テーブルを作成し、mail_address と code カラムを定義します。
     */
    public function up(): void
    {
        // 🚨 テーブル名 mail_auth を指定
        Schema::create('mail_auth', function (Blueprint $table) {
            $table->id(); // Laravel 8以降で推奨される主キー（BIGINT UNSIGNED AUTO_INCREMENT）
            
            // 💡 メールアドレス (ユニーク制約付き)
            $table->string('mail_address')->unique(); 
            
            // 💡 4桁の認証コード
            $table->string('code', 4); 
            
            // 記録日時 (timestampsとは別に、コード生成日時として使用)
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     * テーブルを削除します。
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_auth');
    }
};
