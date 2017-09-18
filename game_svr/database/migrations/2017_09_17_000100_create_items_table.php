<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            // ※ 低レアの独自の性能を持たないアイテムと、一個一個個別の性能を持つアイテム
            $table->enum('type', ['stackable', 'generatable']);
            // ※ categoryはゲームの内容ごとに精査
            $table->enum('category', ['item', 'weapon', 'protector', 'accessary', 'material', 'etc']);
            $table->tinyInteger('rarity');
            $table->integer('weight');
            // ※ 名前やフレーバーはJSONで多言語対応
            // 例）{ "en": "Potion", "jp": "ポーション" }
            $table->text('name');
            $table->text('flavor');
            // ※ アイテム／装備／素材の効果は全てJSONで記入
            // 例）{ "hp": "+50%", "break_limit": 1 }, { "atk": "+10" }
            $table->text('use_effect');
            $table->text('equipping_effect');
            $table->text('material_effect');
            $table->timestamps();

            $table->index(['category', 'rarity', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
