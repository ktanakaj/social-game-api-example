<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserItemsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::create('user_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('count');
            // ※ Diablo式の武器などをイメージした項目。
            //    Holy + Sword とかを実現する。
            $table->text('property_ids');
            $table->timestamps();

            $table->index(['user_id', 'item_id']);
            $table->index(['item_id', 'user_id']);

            $table->foreign('user_id')->references('id')->on('users');
            // ※ スキーマを分けたいので、マスタへの外部キーは貼らない
            // $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::dropIfExists('user_items');
    }
}
