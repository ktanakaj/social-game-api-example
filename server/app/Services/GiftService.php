<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Exceptions\NotFoundException;
use App\Models\Globals\UserGift;
use App\Models\Virtual\ReceivedObject;

/**
 * プレゼント関連の処理を担うサービスクラス。
 */
class GiftService
{
    /**
     * 指定されたプレゼントを受け取る。
     * @param int $userId ユーザーID。
     * @param int $userGiftId 受け取るプレゼントのID。
     * @return ReceivedObject 受け取り結果。
     * @throws NotFoundException プレゼントがユーザーのものとして存在しない場合。
     */
    public function receive(int $userId, int $userGiftId) : ReceivedObject
    {
        DB::transaction(function () use ($userId, $userGiftId, &$received) {
            $userGift = UserGift::lockForUpdate()->findOrFail($userGiftId);
            if ($userGift->user_id != $userId) {
                throw new NotFoundException('The user gift is not belong to me');
            }
            $received = $userGift->receive();
        });
        return $received;
    }

    /**
     * 受け取り可能な全プレゼントを受け取る。
     * @param int $userId ユーザーID。
     * @return array ReceivedObject配列。
     */
    public function receiveAll(int $userId) : array
    {
        DB::transaction(function () use ($userId, &$receivedArray) {
            $receivedArray = UserGift::lockForUpdate()->where('user_id', $userId)->get()->map(function ($m) {
                return $m->receive();
            })->all();
        });
        return $receivedArray;
    }
}
