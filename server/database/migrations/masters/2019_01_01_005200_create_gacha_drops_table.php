<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGachaDropsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::connection('master')->create('gacha_drops', function (Blueprint $table) {
            // ※ このマスタは、個別のIDが特に意味を持たないのでIDはサロゲートキー。
            //    基本的に gacha_id で参照する。
            $table->increments('id');
            $table->unsignedInteger('gacha_id');
            $table->string('object_type', 32);
            $table->unsignedInteger('object_id')->nullable();
            $table->unsignedInteger('count');
            $table->unsignedSmallInteger('weight');
            $table->dateTime('open_at')->nullable();
            $table->dateTime('close_at')->nullable();

            $table->index(['gacha_id', 'open_at', 'close_at']);
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::connection('master')->dropIfExists('gacha_drops');
    }
}
