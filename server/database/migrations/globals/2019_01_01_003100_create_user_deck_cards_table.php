<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDeckCardsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::create('user_deck_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_deck_id');
            $table->unsignedInteger('user_card_id');
            $table->unsignedTinyInteger('position');

            $table->unique(['user_deck_id', 'user_card_id']);
            $table->unique(['user_deck_id', 'position']);
            $table->index('user_card_id');

            $table->foreign('user_deck_id')->references('id')->on('user_decks')->onDelete('cascade');
            $table->foreign('user_card_id')->references('id')->on('user_cards')->onDelete('cascade');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::dropIfExists('user_deck_cards');
    }
}
