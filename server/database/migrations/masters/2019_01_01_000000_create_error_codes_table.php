<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErrorCodesTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        \Schema::connection('master')->create('error_codes', function (Blueprint $table) {
            $table->string('id', 32);
            $table->string('message');
            $table->smallInteger('response_code');
            $table->string('log_level', 16);

            $table->primary('id');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        \Schema::connection('master')->dropIfExists('error_codes');
    }
}
