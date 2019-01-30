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
            $table->unsignedSmallInteger('id');
            $table->string('title');
            $table->text('body');
            $table->dateTime('open_at');
            $table->dateTime('close_at');

            $table->primary('id');
            $table->index(['open_at', 'close_at']);
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
