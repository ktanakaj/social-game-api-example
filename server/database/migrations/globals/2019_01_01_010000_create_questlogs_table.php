<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Enums\QuestStatus;
use App\Models\MigrationUtils;

class CreateQuestlogsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::create('questlogs', function (Blueprint $table) {
            // TODO: 本当は使用したデッキの情報やインゲームのスコアや時間、獲得した報酬などを残したいが、
            //       とりあえず現状はプレイ中か完了済みかの記録のみ。
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('quest_id');
            $table->enum('status', QuestStatus::values());
            $table->dateTime('created_at');
            $table->dateTime('updated_at');

            $table->index(['user_id', 'quest_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['quest_id', 'created_at']);
            $table->index('created_at');
        });

        // 時系列で肥大化する想定なのでパーティション化する
        MigrationUtils::changePrimaryKey('questlogs', ['id', 'created_at']);
        MigrationUtils::createMonthlyPartitions('questlogs', 'created_at');
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::dropIfExists('questlogs');
    }
}
