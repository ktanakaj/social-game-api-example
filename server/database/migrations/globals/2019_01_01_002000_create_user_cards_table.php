<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCardsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::create('user_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('card_id');
            $table->unsignedTinyInteger('count');
            $table->unsignedInteger('exp');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'card_id']);
            $table->index(['card_id', 'exp']);

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::dropIfExists('user_cards');
    }
}
