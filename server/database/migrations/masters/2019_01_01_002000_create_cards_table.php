<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::connection('master')->create('cards', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->tinyInteger('rarity');
            $table->string('name_text_id', 64);
            $table->string('desc_text_id', 64);
            $table->unsignedInteger('max_hp');
            $table->unsignedInteger('attack');
            $table->unsignedInteger('defense');
            $table->unsignedInteger('agility');

            $table->primary('id');
            $table->index(['rarity', 'id']);
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::connection('master')->dropIfExists('cards');
    }
}
