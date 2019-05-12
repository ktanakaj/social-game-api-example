<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Enums\AchievementType;

class CreateAchievementsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        \Schema::connection('master')->create('achievements', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->enum('type', AchievementType::values());
            $table->string('text_id', 64);
            $table->string('condition', 32);
            $table->unsignedInteger('score');
            $table->text('options')->nullable();
            $table->string('object_type', 32);
            $table->unsignedInteger('object_id')->nullable();
            $table->unsignedInteger('count');
            $table->dateTime('open_at')->nullable();
            $table->dateTime('close_at')->nullable();

            $table->primary('id');
            $table->index(['type', 'condition']);
            $table->index(['open_at', 'close_at']);
        });
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        \Schema::connection('master')->dropIfExists('achievements');
    }
}
