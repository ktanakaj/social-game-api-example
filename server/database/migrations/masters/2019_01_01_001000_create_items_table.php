<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Enums\ItemType;

class CreateItemsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::connection('master')->create('items', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->enum('type', ItemType::values());
            $table->tinyInteger('rarity');
            $table->string('name_text_id', 64);
            $table->string('help_text_id', 64);
            $table->text('effect');
            $table->dateTime('expired_at')->nullable();

            $table->primary('id');
            $table->index(['type', 'rarity', 'id']);
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::connection('master')->dropIfExists('items');
    }
}
