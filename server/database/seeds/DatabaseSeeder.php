<?php

use Illuminate\Database\Seeder;
use App\Models\Masters\ErrorCode;
use App\Models\Masters\Item;
use App\Models\Masters\ItemProperty;
use App\Models\Masters\GiftMessage;
use App\Models\Admins\Administrator;

class DatabaseSeeder extends Seeder
{
    /**
     * DB初期データ生成。
     */
    public function run() : void
    {
        $admin = new Administrator();
        $admin->email = 'admin';
        $admin->password = bcrypt('admin01');
        $admin->role = 0;
        $admin->note = '初期管理者';
        $admin->save();

        // FIXME: マスタインポートの仕組みを作成して、以下はマスタに移動する
        $errorCode500 = new ErrorCode();
        $errorCode500->id = 'INTERNAL_SERVER_ERROR';
        $errorCode500->message = 'Internal Server Error';
        $errorCode500->response_code = 500;
        $errorCode500->log_level = 'error';
        $errorCode500->save();

        $errorCode400 = new ErrorCode();
        $errorCode400->id = 'BAD_REQUEST';
        $errorCode400->message = 'Bad Request';
        $errorCode400->response_code = 400;
        $errorCode400->log_level = 'debug';
        $errorCode400->save();

        $errorCode401 = new ErrorCode();
        $errorCode401->id = 'UNAUTHORIZED';
        $errorCode401->message = 'Unauthorized';
        $errorCode401->response_code = 401;
        $errorCode401->log_level = 'debug';
        $errorCode401->save();

        $errorCode403 = new ErrorCode();
        $errorCode403->id = 'FORBIDDEN';
        $errorCode403->message = 'Forbidden';
        $errorCode403->response_code = 403;
        $errorCode403->log_level = 'debug';
        $errorCode403->save();

        $errorCode404 = new ErrorCode();
        $errorCode404->id = 'NOT_FOUND';
        $errorCode404->message = 'Not Found';
        $errorCode404->response_code = 404;
        $errorCode404->log_level = 'debug';
        $errorCode404->save();

        $errorCode409 = new ErrorCode();
        $errorCode409->id = 'CONFLICT';
        $errorCode409->message = 'Conflict';
        $errorCode409->response_code = 409;
        $errorCode409->log_level = 'debug';
        $errorCode409->save();

        $item = new Item();
        $item->id = 1;
        $item->type = 'stackable';
        $item->category = 'item';
        $item->rarity = 1;
        $item->weight = 1;
        $item->name = ['en' => 'Potion', 'jp' => 'ポーション'];
        $item->flavor = ['en' => 'HP potion.', 'jp' => '普通の回復薬。'];
        $item->use_effect = ['hp' => '+30%', 'break_limit' => 1];
        $item->save();

        $item = new Item();
        $item->id = 2;
        $item->type = 'stackable';
        $item->category = 'weapon';
        $item->rarity = 1;
        $item->weight = 10;
        $item->name = ['en' => 'Short Sword', 'jp' => 'ショートソード'];
        $item->flavor = ['en' => 'Cheaper sword.', 'jp' => '短い剣。'];
        $item->equipping_effect = ['atk' => '+10'];
        $item->save();

        $item = new Item();
        $item->id = 3;
        $item->type = 'generatable';
        $item->category = 'weapon';
        $item->rarity = 2;
        $item->weight = 15;
        $item->name = ['en' => 'Knight Sword', 'jp' => 'ナイトソード'];
        $item->flavor = ['en' => 'Strong sword.', 'jp' => '騎士の剣。'];
        $item->equipping_effect = ['atk' => '+20'];
        $item->save();

        $itemProperty = new ItemProperty();
        $itemProperty->id = 1;
        $itemProperty->type = 'prefix';
        $itemProperty->category = 'weapon';
        $itemProperty->rarity = 2;
        $itemProperty->name = ['en' => 'Holy', 'jp' => '聖なる'];
        $itemProperty->equipping_effect = ['atk' => '+3'];
        $itemProperty->save();

        $msg = new GiftMessage();
        $msg->id = 1;
        $msg->message = '報酬';
        $msg->save();

        $msg = new GiftMessage();
        $msg->id = 12;
        $msg->message = '不具合のお詫び';
        $msg->save();
    }
}
