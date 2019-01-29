<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdministratorsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::connection('admin')->create('administrators', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 191)->unique();
            $table->string('password')->nullable();
            $table->tinyInteger('role');
            $table->text('note')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::connection('admin')->dropIfExists('administrators');
    }
}
