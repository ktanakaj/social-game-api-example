<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDropsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        \Schema::connection('master')->create('drops', function (Blueprint $table) {
            // ※ このマスタは、個別のIDが特に意味を持たないのでIDはサロゲートキー。
            //    基本的に drop_set_id で参照する。
            $table->increments('id');
            $table->unsignedInteger('drop_set_id');
            $table->unsignedInteger('group')->nullable();
            $table->unsignedTinyInteger('rate');
            $table->string('object_type', 32);
            $table->unsignedInteger('object_id')->nullable();
            $table->unsignedInteger('count');
            $table->unsignedSmallInteger('range')->nullable();

            $table->index(['drop_set_id', 'group']);
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        \Schema::connection('master')->dropIfExists('drops');
    }
}
