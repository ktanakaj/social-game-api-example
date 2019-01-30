<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemPropertiesTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::connection('master')->create('item_properties', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->enum('type', ['prefix', 'suffix']);
            $table->enum('category', ['item', 'weapon', 'protector', 'accessary', 'material']);
            $table->tinyInteger('rarity');
            $table->boolean('enable')->default(true);
            // ※ プレフィックス／サフィックスはJSONで多言語対応
            // 例）{ "en": "Holy", "jp": "聖なる" }
            $table->text('name');
            // ※ ボーナス効果は全てJSONで記入
            // 例）{ "atk": "+5" }
            $table->text('use_effect');
            $table->text('equipping_effect');
            $table->text('material_effect');

            $table->primary('id');
            $table->index(['category', 'rarity', 'type']);
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::connection('master')->dropIfExists('item_properties');
    }
}
