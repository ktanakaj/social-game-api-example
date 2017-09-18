<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->smallIncrements('id');
            // ※ 見出しや本文はJSONで多言語対応
            $table->text('title');
            $table->text('body');
            $table->dateTime('open_date');
            $table->dateTime('close_date');
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
        Schema::dropIfExists('news');
    }
}
