<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
            $table->foreign('item_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_items');
    }
}
