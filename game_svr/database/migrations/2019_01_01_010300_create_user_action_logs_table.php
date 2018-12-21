<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\MigrationUtils;

class CreateUserActionLogsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::create('user_action_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            // ※ typeはenumだが頻繁に追加される想定なのでVARCHARで定義
            $table->string('type', 32);
            $table->text('data');
            $table->dateTime('created_at');

            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });

        // 時系列で増える履歴なのでパーティション化する
        DB::statement("ALTER TABLE `user_action_logs` DROP PRIMARY KEY, ADD PRIMARY KEY (`id`, `created_at`)");
        MigrationUtils::createDatePartition('user_action_logs', 'created_at');
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::dropIfExists('user_action_logs');
    }
}
