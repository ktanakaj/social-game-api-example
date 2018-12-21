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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->bigInteger('game_coin')->default(0);
            $table->bigInteger('special_coin')->default(0);
            $table->bigInteger('free_special_coin')->default(0);
            $table->integer('level')->default(1);
            $table->bigInteger('exp')->default(0);
            $table->dateTime('last_login')->nullable();
            $table->timestamps();
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
