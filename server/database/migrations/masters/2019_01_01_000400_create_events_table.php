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
            $table->unsignedSmallInteger('id');
            // ※ typeはenumだが頻繁に追加される想定なのでVARCHARで定義
            $table->string('type', 32);
            $table->dateTime('open_at');
            $table->dateTime('close_at');
            $table->string('title');

            $table->primary('id');
            $table->index(['open_at', 'close_at']);
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
