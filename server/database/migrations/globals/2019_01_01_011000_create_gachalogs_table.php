<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\MigrationUtils;

class CreateGachalogsTable extends Migration
{
    /**
     * マイグレーション実行。
     */
    public function up() : void
    {
        Schema::create('gachalogs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');
            // ※ gacha_id は gacha_price_id から辿れるが、集計の便宜上定義
            $table->unsignedInteger('gacha_id');
            $table->unsignedInteger('gacha_price_id');
            $table->dateTime('created_at');

            $table->index(['user_id', 'gacha_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['gacha_id', 'created_at']);
            $table->index('created_at');
        });

        // 時系列で肥大化する想定なのでパーティション化する
        MigrationUtils::changePrimaryKey('gachalogs', ['id', 'created_at']);
        MigrationUtils::createDatePartition('gachalogs', 'created_at');
    }

    /**
     * マイグレーション取消。
     */
    public function down() : void
    {
        Schema::dropIfExists('gachalogs');
    }
}
