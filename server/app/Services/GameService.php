<?php

namespace App\Services;

use App\Models\Globals\Questlog;
use App\Models\Globals\User;
use App\Models\Globals\UserQuest;
use App\Models\Masters\Drop;
use App\Models\Masters\Quest;
use App\Models\ObjectReceiver;

/**
 * ゲーム関連の処理を担うサービスクラス。
 */
class GameService
{
    /**
     * ゲームを開始する。
     * @param int $userId ユーザーID。
     * @param array $params ゲーム開始情報。
     * @return array ゲーム開始結果。
     */
    public function start(int $userId, array $params) : array
    {
        \DB::transaction(function () use ($userId, $params, &$result) {
            // スタミナを消費して履歴を作成
            $quest = Quest::findOrFail($params['questId']);
            $user = User::lockForUpdate()->findOrFail($userId);
            $user->stamina -= $quest->stamina;
            $user->save();
            $questlog = Questlog::create(['user_id' => $userId, 'quest_id' => $quest->id]);
            $result = [
                'questlogId' => $questlog->id,
                'stamina' => $user->stamina,
            ];
        });
        return $result;
    }

    /**
     * ゲームを終了する。
     * @param int $userId ユーザーID。
     * @param array $params ゲーム終了情報。
     * @return array ゲームの報酬のReceivedInfo配列。
     */
    public function end(int $userId, array $params) : array
    {
        \DB::transaction(function () use ($userId, $params, &$receivedArray) {
            $receivedArray = [];

            // 履歴を更新して報酬を付与
            // TODO: 本当はもっといろいろある想定だが、現状仮のインゲームは内容が何もないので履歴と報酬だけ
            $log = Questlog::lockForUpdate()->findOrFail($params['questlogId']);
            $log->status = $params['status'];
            $log->save();

            if ($log->status !== 'succeed') {
                return;
            }

            $userQuest = UserQuest::lockForUpdate()->firstOrNew(['user_id' => $userId, 'quest_id' => $log->quest_id]);
            ++$userQuest->count;
            $userQuest->save();

            $quest = $log->quest;
            $dropSetId = $userQuest->count === 1 ? $quest->first_drop_set_id : $quest->retry_drop_set_id;
            if ($dropSetId) {
                foreach (Drop::lot($dropSetId) as $info) {
                    $receivedArray[] = ObjectReceiver::receive($userId, $info);
                }
            }
        });
        return $receivedArray;
    }
}
