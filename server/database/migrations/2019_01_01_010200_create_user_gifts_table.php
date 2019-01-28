<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\MigrationUtils;

class CreateUserGiftsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::create('user_gifts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('message_id');
            $table->text('data');
            $table->dateTime('created_at');
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
        });

        // 時系列で肥大化する想定なのでパーティション化する
        DB::statement("ALTER TABLE `user_gifts` DROP PRIMARY KEY, ADD PRIMARY KEY (`id`, `created_at`)");
        MigrationUtils::createDatePartition('user_gifts', 'created_at');
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::dropIfExists('user_gifts');
    }
}
