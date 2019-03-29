<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Enums\ObjectType;
use App\Models\Globals\Gachalog;
use App\Models\Globals\User;
use App\Models\Globals\UserItem;
use App\Models\Masters\Gacha;
use App\Models\Masters\GachaPrice;
use App\Models\ObjectReceiver;

/**
 * ガチャ関連の処理を担うサービスクラス。
 */
class GachaService
{
    /**
     * 利用可能なガチャの一覧を取得する。
     * @param int $userId ユーザーID。
     * @return array ガチャ情報配列。
     */
    public function findGachas(int $userId) : array
    {
        // 有効なガチャを返す
        // TODO: 出来ればユーザーが実行可能なガチャのみを絞り込んで返す。
        //       初回無料とかをサーバー側で判定することを意図しているが、
        //       ただ課金コインとかは所持数が足りなくても表示したいので、
        //       もうちょっと課題の整理が必要かも？
        //       （everytime=trueのものは有効なら引けなくても常時返すとか？）
        return Gacha::active()->with(['prices' => function ($query) {
            return $query->active();
        }])->get()->all();
    }

    /**
     * ガチャの詳細を取得する。
     * @param int $gachaId ガチャID。
     * @return array ガチャ情報。
     */
    public function findGacha(int $gachaId) : array
    {
        // 基本は有効なガチャマスタを返すだけだが、ガチャ排出物だけ確率計算したものを取得する
        $gacha = Gacha::active()->with(['prices' => function ($query) {
            return $query->active();
        }])->findOrFail($gachaId);
        $result = $gacha->toArray();
        $result['drops'] = $gacha->getRates()->makeHidden('weight');
        return $result;
    }

    /**
     * ガチャを抽選する。
     * @param int $userId ユーザーID。
     * @param array $params ガチャ抽選情報。
     * @return array 抽選結果のReceivedInfo配列。
     */
    public function lot(int $userId, array $params) : array
    {
        DB::transaction(function () use ($userId, $params, &$receivedArray) {
            $gachaPrice = GachaPrice::active()->findOrFail($params['gachaPriceId']);
            $gacha = $gachaPrice->gacha;

            // 料金を消費
            // TODO: この辺も ObjectReceiver みたいな仕組作りたい。またはそっちも含めて整理したい
            switch ($gachaPrice->object_type) {
                case ObjectType::GAME_COIN:
                    $user = User::lockForUpdate()->findOrFail($userId);
                    $user->game_coins -= $gachaPrice->prices * $params['count'];
                    $user->save();
                    break;
                case ObjectType::SPECIAL_COIN:
                    $user = User::lockForUpdate()->findOrFail($userId);
                    // FIXME: 課金/非課金のコイン両方を見る
                    $user->free_special_coins -= $gachaPrice->prices * $params['count'];
                    $user->save();
                    break;
                case ObjectType::ITEM:
                    $userItem = UserItem::lockForUpdate()->where('user_id', $userId)->where('item_id', $gachaPrice->object_id)->firstOrFail();
                    $userItem->count -= $gachaPrice->prices * $params['count'];
                    $userItem->save();
                    break;
            }

            // ガチャを抽選して履歴を記録
            $receivedArray = [];
            for ($i = 0; $i < $params['count']; $i++) {
                $lotted = [];
                for ($j = 0; $j < $gachaPrice->times; $j++) {
                    $info = $gacha->lot();
                    $lotted[] = $info;
                    $receivedArray[] = ObjectReceiver::receive($userId, $info);
                }
                $gachalog = Gachalog::create(['user_id' => $userId, 'gacha_id' => $gacha->id, 'gacha_price_id' => $gachaPrice->id]);
                $gachalog->drops()->createMany(array_map(function ($info) {
                    return [
                        'object_type' => $info->type,
                        'object_id' => $info->id,
                        'count' => $info->count,
                    ];
                }, $lotted));
            }
        });
        return $receivedArray;
    }
}
