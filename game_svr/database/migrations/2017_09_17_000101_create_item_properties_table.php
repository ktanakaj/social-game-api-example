<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_properties', function (Blueprint $table) {
            $table->increments('id');
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
            $table->timestamps();

            $table->index(['category', 'rarity', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_properties');
    }
}
