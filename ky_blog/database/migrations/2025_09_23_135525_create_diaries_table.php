<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diaries', function (Blueprint $table) {
            $table->id(); // idカラム（主キー、自動インクリメント）
            // 1. diaries テーブルに外部キー用のカラムを作成 (accounts.idに合わせてstring型で)
            $table->string('user_id', 255); 
            // 2. 外部キー制約を設定し、参照先を accounts テーブルの id カラムに明示する
            $table->foreign('user_id')
            ->references('id')->on('accounts')
            ->onDelete('cascade'); 
            $table->string('title'); // タイトル
            $table->text('content'); // コンテンツ
            $table->mediumText('image')->nullable();  // 画像（nullableはnullを許可）
            $table->decimal('latitude', 10, 8)->nullable(); // 小数点以下8桁の精度で緯度を保存（NULLを許可）
            $table->decimal('longitude', 11, 8)->nullable(); // 小数点以下8桁の精度で経度を保存（NULLを許可）
            $table->string('place', 512)->nullable();  // 場所を保存するためのカラムを追加（長めの文字列を許容）
            $table->boolean('is_public')->default(false); // みんなの日記に公開するかフラグ: 0=非公開, 1=公開 (デフォルトは非公開)
            $table->timestamps(); // created_atとupdated_atカラム
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diaries');
    }
};