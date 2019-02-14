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
            $table->string('text_id', 64);
            $table->text('text_options');
            $table->string('object_type', 32);
            $table->unsignedInteger('object_id')->nullable();
            $table->unsignedInteger('count');
            $table->dateTime('created_at');
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index(['text_id', 'user_id', 'created_at']);
            $table->index(['object_type', 'object_id', 'created_at']);
        });

        // 時系列で肥大化する想定なのでパーティション化する
        MigrationUtils::changePrimaryKey('user_gifts', ['id', 'created_at']);
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
