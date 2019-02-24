<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParametersTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::connection('master')->create('parameters', function (Blueprint $table) {
            $table->string('id', 64);
            $table->string('type', 16);
            $table->text('value')->nullable();
            $table->string('comment')->nullable();

            $table->primary('id');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::connection('master')->dropIfExists('parameters');
    }
}
