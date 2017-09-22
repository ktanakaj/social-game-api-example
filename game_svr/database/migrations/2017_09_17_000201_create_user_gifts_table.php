<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
        DB::statement("ALTER TABLE `user_gifts` PARTITION BY RANGE COLUMNS(`created_at`) (
            PARTITION p201801 VALUES LESS THAN ('2018-02-01 00:00:00'),
            PARTITION p201802 VALUES LESS THAN ('2018-03-01 00:00:00'),
            PARTITION p201803 VALUES LESS THAN ('2018-04-01 00:00:00'),
            PARTITION p201804 VALUES LESS THAN ('2018-05-01 00:00:00'),
            PARTITION p201805 VALUES LESS THAN ('2018-06-01 00:00:00'),
            PARTITION p201806 VALUES LESS THAN ('2018-07-01 00:00:00'),
            PARTITION p201807 VALUES LESS THAN ('2018-08-01 00:00:00'),
            PARTITION p201808 VALUES LESS THAN ('2018-09-01 00:00:00'),
            PARTITION p201809 VALUES LESS THAN ('2018-10-01 00:00:00'),
            PARTITION p201810 VALUES LESS THAN ('2018-11-01 00:00:00'),
            PARTITION p201811 VALUES LESS THAN ('2018-12-01 00:00:00'),
            PARTITION p201812 VALUES LESS THAN ('2019-01-01 00:00:00'),
            PARTITION p201901 VALUES LESS THAN ('2019-02-01 00:00:00'),
            PARTITION p201902 VALUES LESS THAN ('2019-03-01 00:00:00'),
            PARTITION p201903 VALUES LESS THAN ('2019-04-01 00:00:00'),
            PARTITION p201904 VALUES LESS THAN ('2019-05-01 00:00:00'),
            PARTITION p201905 VALUES LESS THAN ('2019-06-01 00:00:00'),
            PARTITION p201906 VALUES LESS THAN ('2019-07-01 00:00:00'),
            PARTITION p201907 VALUES LESS THAN ('2019-08-01 00:00:00'),
            PARTITION p201908 VALUES LESS THAN ('2019-09-01 00:00:00'),
            PARTITION p201909 VALUES LESS THAN ('2019-10-01 00:00:00'),
            PARTITION p201910 VALUES LESS THAN ('2019-11-01 00:00:00'),
            PARTITION p201911 VALUES LESS THAN ('2019-12-01 00:00:00'),
            PARTITION p201912 VALUES LESS THAN ('2020-01-01 00:00:00'),
            PARTITION p202001 VALUES LESS THAN ('2020-02-01 00:00:00'),
            PARTITION p202002 VALUES LESS THAN ('2020-03-01 00:00:00'),
            PARTITION p202003 VALUES LESS THAN ('2020-04-01 00:00:00'),
            PARTITION p202004 VALUES LESS THAN ('2020-05-01 00:00:00'),
            PARTITION p202005 VALUES LESS THAN ('2020-06-01 00:00:00'),
            PARTITION p202006 VALUES LESS THAN ('2020-07-01 00:00:00'),
            PARTITION p202007 VALUES LESS THAN ('2020-08-01 00:00:00'),
            PARTITION p202008 VALUES LESS THAN ('2020-09-01 00:00:00'),
            PARTITION p202009 VALUES LESS THAN ('2020-10-01 00:00:00'),
            PARTITION p202010 VALUES LESS THAN ('2020-11-01 00:00:00'),
            PARTITION p202011 VALUES LESS THAN ('2020-12-01 00:00:00'),
            PARTITION p202012 VALUES LESS THAN ('2021-01-01 00:00:00'),
            PARTITION pmax VALUES LESS THAN ('2038-01-01 00:00:00')
        )");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_gifts');
    }
}
