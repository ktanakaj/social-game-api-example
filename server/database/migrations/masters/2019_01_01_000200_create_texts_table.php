<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        \Schema::connection('master')->create('texts', function (Blueprint $table) {
            $table->string('id', 64);
            $table->string('text_en');
            $table->string('text_ja');

            $table->primary('id');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        \Schema::connection('master')->dropIfExists('texts');
    }
}
