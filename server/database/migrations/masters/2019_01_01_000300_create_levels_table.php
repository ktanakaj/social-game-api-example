<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        \Schema::connection('master')->create('levels', function (Blueprint $table) {
            // ※ id=level
            $table->unsignedSmallInteger('level');
            $table->unsignedInteger('exp');
            $table->unsignedSmallInteger('max_stamina');

            $table->primary('level');
            $table->index('exp');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        \Schema::connection('master')->dropIfExists('levels');
    }
}
