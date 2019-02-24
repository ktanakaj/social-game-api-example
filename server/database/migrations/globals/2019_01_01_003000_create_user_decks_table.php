<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDecksTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::create('user_decks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('no');
            $table->timestamps();

            $table->unique(['user_id', 'no']);

            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('last_selected_deck_id')->references('id')->on('user_decks')->onDelete('set null');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['last_selected_deck_id']);
        });
        Schema::dropIfExists('user_decks');
    }
}
