<?php

namespace App\Services;

use App\Enums\AchievementCondition;
use App\Models\Globals\UserAchievement;
use App\Models\Virtual\ReceivedInfo;

/**
 * アチーブメント関連の処理を担うサービスクラス。
 */
class AchievementService
{
    /**
     * 有効なアチーブメント一覧を取得&達成状況を更新する。
     * @param int $userId ユーザーID。
     * @return array UserAchievement配列。
     */
    public function findAndUpdate(int $userId) : array
    {
        // 有効なアチーブメント一覧を取得する。
        // 絶対値系などの一部アチーブメントは、このタイミングでデータを更新する。
        // （絶対値系は、マスタが公開されてこのAPIを呼ぶまでに更新するタイミングが無いので、ここで集計する必要がある。）
        \DB::transaction(function () use ($userId, &$userAchievements) {
            $userAchievements = UserAchievement::findActiveOrNew($userId);
            foreach ($userAchievements as $userAchievement) {
                // TODO: ObjectReceiverみたいにどこかに整理する
                switch ($userAchievement->achievement->condition) {
                    case AchievementCondition::LEVEL:
                        $userAchievement->score = $userAchievement->user->level;
                        break;
                }
                $userAchievement->save();
            }
        });
        return $userAchievements->all();
    }

    /**
     * 指定されたアチーブメント報酬を受け取る。
     * @param int $userId ユーザーID。
     * @param int $userAchievementId 受け取るアチーブメント報酬のID。
     * @return ReceivedInfo 受け取り結果。
     * @throws \App\Exceptions\BadRequestException アチーブメントを達成していない場合。
     */
    public function receive(int $userId, int $userAchievementId) : ReceivedInfo
    {
        \DB::transaction(function () use ($userId, $userAchievementId, &$received) {
            $userAchievement = UserAchievement::lockForUpdate()->where('user_id', $userId)->findOrFail($userAchievementId);
            $received = $userAchievement->receive();
        });
        return $received;
    }

    /**
     * 受け取り可能な全アチーブメント報酬を受け取る。
     * @param int $userId ユーザーID。
     * @return array ReceivedInfo配列。
     */
    public function receiveAll(int $userId) : array
    {
        \DB::transaction(function () use ($userId, &$receivedArray) {
            $receivedArray = UserAchievement::findActiveOrNew($userId)->filter(function ($m) {
                return !$m->received && $m->isAchieved();
            })->map(function ($m) {
                return $m->receive();
            })->all();
        });
        return array_values($receivedArray);
    }
}
