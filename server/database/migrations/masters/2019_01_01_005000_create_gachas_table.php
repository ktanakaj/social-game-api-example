<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGachasTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        \Schema::connection('master')->create('gachas', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('name_text_id', 64);
            $table->string('desc_text_id', 64);

            $table->primary('id');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        \Schema::connection('master')->dropIfExists('gachas');
    }
}
