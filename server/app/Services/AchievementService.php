<?php

namespace App\Services;

use App\Enums\AchievementCondition;
use App\Models\Globals\UserAchievement;
use App\Models\Masters\Achievement;
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
        // マスタを元に現在有効なアチーブメントの一覧を取得する。
        // デイリーやウィークリーの古いアチーブメントはこのタイミングで上書き（DELETE→INSERT）する。
        // また、絶対値系などの一部アチーブメントも、このタイミングでデータを更新する。
        // （絶対値系もリアルタイム更新できるが、リアルタイムでやる必要が無いし、
        //   API的にも一覧を呼ばずに受け取りだけ呼ぶのは考えにくいので、ここでやってしまう。）
        \DB::transaction(function () use ($userId, &$userAchievements) {
            $all = Achievement::all()->active();
            $userAchievementByIds = UserAchievement::lockForUpdate()->where('user_id', $userId)->whereIn('achievement_id', $all->pluck('id'))->get()->keyBy('achievement_id');
            foreach ($all as $achievement) {
                if ($userAchievement = $userAchievementByIds->get($achievement->id)) {
                    // デイリー／ウィークリーの期限切れは削除
                    if ($userAchievement->isExpired()) {
                        $userAchievement->delete();
                        $userAchievement = null;
                    }
                }
                if (!$userAchievement) {
                    $userAchievement = new UserAchievement(['user_id' => $userId, 'achievement_id' => $achievement->id]);
                    $userAchievementByIds->put($achievement->id, $userAchievement);
                }

                // 一部の条件はこのタイミングで集計
                // TODO: ObjectReceiverみたいにどこかに整理する
                switch ($userAchievement->achievement->condition) {
                    case AchievementCondition::LEVEL:
                        $userAchievement->score = $userAchievement->user->level;
                        break;
                }

                $userAchievement->save();
            }
            $userAchievements = $userAchievementByIds->values();
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
            $userAchievements = array_filter($this->findAndUpdate($userId), function ($m) {
                return !$m->received && $m->isAchieved();
            });
            $receivedArray = array_map(function ($m) {
                return $m->receive();
            }, $userAchievements);
        });
        return array_values($receivedArray);
    }
}
