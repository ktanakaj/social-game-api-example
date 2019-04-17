<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        \Schema::connection('master')->create('quests', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->unsignedInteger('previous_id')->nullable();
            // ※ その他にもdailyやweeklyとかもありそう
            $table->enum('type', ['main', 'sub', 'event']);
            $table->string('name_text_id', 64);
            $table->string('desc_text_id', 64);
            $table->dateTime('open_at')->nullable();
            $table->dateTime('close_at')->nullable();
            $table->unsignedSmallInteger('stamina');
            $table->unsignedInteger('first_drop_set_id')->nullable();
            $table->unsignedInteger('retry_drop_set_id')->nullable();
            // ※ 本当は他にもインゲームのマップや敵、ストーリーといった多くの情報がある想定

            $table->primary('id');
            $table->index(['open_at', 'close_at']);
            $table->index('previous_id');
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        \Schema::connection('master')->dropIfExists('quests');
    }
}
