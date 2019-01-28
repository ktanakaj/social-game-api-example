<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGiftMessagesTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::connection('master')->create('gift_messages', function (Blueprint $table) {
            $table->increments('id');
            // ※ メッセージはJSONで多言語対応
            // 例）{ "en": "Message A", "jp": "メッセージA" }
            $table->text('message');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::connection('master')->dropIfExists('gift_messages');
    }
}
