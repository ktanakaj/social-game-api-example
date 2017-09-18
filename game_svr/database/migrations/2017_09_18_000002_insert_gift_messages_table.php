<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use App\Models\GiftMessage;

class InsertGiftMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // テスト用マスタの登録
        $msg = new GiftMessage();
        $msg->message = ['en' => 'Reward', 'jp' => '報酬'];
        $msg->save();

        $msg = new GiftMessage();
        $msg->message = ['en' => 'Fill a deficit', 'jp' => '不具合のお詫び'];
        $msg->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('gift_messages')->truncate();
        Schema::enableForeignKeyConstraints();
    }
}
