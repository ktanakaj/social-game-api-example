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
            $table->unsignedBigInteger('game_coins');
            $table->unsignedInteger('special_coins');
            $table->unsignedInteger('free_special_coins');
            $table->unsignedBigInteger('exp');
            $table->unsignedInteger('stamina');
            $table->dateTime('stamina_updated_at')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->timestamps();
            $table->softDeletes();

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
