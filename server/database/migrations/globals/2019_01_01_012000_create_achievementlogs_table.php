<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\MigrationUtils;

class CreateAchievementlogsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        \Schema::create('achievementlogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('achievement_id');
            $table->dateTime('created_at');

            $table->index(['user_id', 'achievement_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['achievement_id', 'created_at']);
            $table->index('created_at');
        });

        // 時系列で肥大化する想定なのでパーティション化する
        MigrationUtils::changePrimaryKey('achievementlogs', ['id', 'created_at']);
        MigrationUtils::createMonthlyPartitions('achievementlogs', 'created_at');
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        \Schema::dropIfExists('achievementlogs');
    }
}
