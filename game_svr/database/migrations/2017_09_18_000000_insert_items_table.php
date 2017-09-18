<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use App\Models\Item;

class InsertItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // テスト用マスタの登録
        $item = new Item();
        $item->type = 'stackable';
        $item->category = 'item';
        $item->rarity = 1;
        $item->weight = 1;
        $item->name = ['en' => 'Potion', 'jp' => 'ポーション'];
        $item->flavor = ['en' => 'HP potion.', 'jp' => '普通の回復薬。'];
        $item->use_effect = ['hp' => '+30%', 'break_limit' => 1];
        $item->save();

        $item = new Item();
        $item->type = 'stackable';
        $item->category = 'weapon';
        $item->rarity = 1;
        $item->weight = 10;
        $item->name = ['en' => 'Short Sword', 'jp' => 'ショートソード'];
        $item->flavor = ['en' => 'Cheaper sword.', 'jp' => '短い剣。'];
        $item->equipping_effect = ['atk' => '+10'];
        $item->save();

        $item = new Item();
        $item->type = 'generatable';
        $item->category = 'weapon';
        $item->rarity = 2;
        $item->weight = 15;
        $item->name = ['en' => 'Knight Sword', 'jp' => 'ナイトソード'];
        $item->flavor = ['en' => 'Strong sword.', 'jp' => '騎士の剣。'];
        $item->equipping_effect = ['atk' => '+20'];
        $item->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('items')->truncate();
        Schema::enableForeignKeyConstraints();
    }
}
