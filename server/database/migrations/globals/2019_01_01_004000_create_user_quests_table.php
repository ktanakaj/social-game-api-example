<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserQuestsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        \Schema::create('user_quests', function (Blueprint $table) {
            // ※ もしインゲームにスコアがあるなら、ハイスコアとかも記録したい
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('quest_id');
            $table->unsignedInteger('count');
            $table->timestamps();

            $table->index(['user_id', 'quest_id']);
            $table->index('quest_id');

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        \Schema::dropIfExists('user_quests');
    }
}
