<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::connection('master')->create('events', function (Blueprint $table) {
            $table->smallIncrements('id');
            // ※ typeはenumだが頻繁に追加される想定なのでVARCHARで定義
            $table->string('type', 32);
            $table->dateTime('open_date');
            $table->dateTime('close_date');
            // ※ イベント名はJSONで多言語対応
            $table->text('title');

            $table->index(['open_date', 'close_date']);
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::connection('master')->dropIfExists('events');
    }
}
