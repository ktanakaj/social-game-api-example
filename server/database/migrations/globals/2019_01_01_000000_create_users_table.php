<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token');
            $table->string('name', 191);
            $table->bigInteger('game_coin');
            $table->bigInteger('special_coin');
            $table->bigInteger('free_special_coin');
            $table->bigInteger('stamina');
            $table->bigInteger('exp');
            $table->dateTime('last_login')->nullable();
            $table->timestamps();

            $table->index('name', 'id');
            $table->index('last_login');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::dropIfExists('users');
    }
}
