<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use App\Models\ItemProperty;

class InsertItemPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // テスト用マスタの登録
        $itemProperty = new ItemProperty();
        $itemProperty->type = 'prefix';
        $itemProperty->category = 'weapon';
        $itemProperty->rarity = 2;
        $itemProperty->name = ['en' => 'Holy', 'jp' => '聖なる'];
        $itemProperty->equipping_effect = ['atk' => '+3'];
        $itemProperty->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('item_properties')->truncate();
        Schema::enableForeignKeyConstraints();
    }
}
