<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\MigrationUtils;

class CreateGachalogDropsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::create('gachalog_drops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('gachalog_id');
            $table->string('object_type', 32);
            $table->unsignedInteger('object_id')->nullable();
            $table->unsignedInteger('count');
            $table->dateTime('created_at');

            $table->index('gachalog_id');
            $table->index('created_at');
        });

        // 時系列で肥大化する想定なのでパーティション化する
        MigrationUtils::changePrimaryKey('gachalog_drops', ['id', 'created_at']);
        MigrationUtils::createDatePartition('gachalog_drops', 'created_at');
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::dropIfExists('gachalog_drops');
    }
}
