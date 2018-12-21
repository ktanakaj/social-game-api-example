<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::connection('master')->create('news', function (Blueprint $table) {
            $table->smallIncrements('id');
            // ※ 見出しや本文はJSONで多言語対応
            $table->text('title');
            $table->text('body');
            $table->dateTime('open_date');
            $table->dateTime('close_date');

            $table->index(['open_date', 'close_date']);
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::connection('master')->dropIfExists('news');
    }
}
