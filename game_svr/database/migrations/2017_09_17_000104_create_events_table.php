<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->smallIncrements('id');
            // ※ typeはenumだが頻繁に追加される想定なのでVARCHARで定義
            $table->string('type', 32);
            $table->dateTime('open_date');
            $table->dateTime('close_date');
            // ※ イベント名はJSONで多言語対応
            $table->text('title');
            $table->timestamps();

            $table->index(['open_date', 'close_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
