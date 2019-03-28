<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGachaPricesTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::connection('master')->create('gacha_prices', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unsignedInteger('gacha_id');
            $table->string('object_type', 32);
            $table->unsignedInteger('object_id')->nullable();
            $table->unsignedInteger('prices');
            $table->unsignedSmallInteger('times');
            $table->dateTime('open_at')->nullable();
            $table->dateTime('close_at')->nullable();

            $table->primary('id');
            $table->index(['gacha_id', 'open_at', 'close_at']);
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::connection('master')->dropIfExists('gacha_prices');
    }
}
