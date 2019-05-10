<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAchievementsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        \Schema::create('user_achievements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('achievement_id');
            $table->unsignedInteger('score');
            $table->boolean('received');
            $table->timestamps();

            $table->unique(['user_id', 'achievement_id']);
            $table->index(['achievement_id', 'score']);

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        \Schema::dropIfExists('user_achievements');
    }
}
