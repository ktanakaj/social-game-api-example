<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Events\Dispatcher;
use App\Enums\AchievementCondition;
use App\Enums\QuestStatus;
use App\Events\QuestlogSaved;
use App\Models\Globals\UserAchievement;

/**
 * アチーブメント更新用のイベントサブスクライバー。
 */
class AchievementEventSubscriber
{
    /**
     * クエストログ更新時の処理。
     * @param QuestlogSaved $event イベント。
     */
    public function onQuestlogSaved(QuestlogSaved $event) : void
    {
        // クエストプレイ状況に関連するアチーブメントを更新する
        $questlog = $event->log;
        $oldlog = $event->original;

        // 任意のクエストクリア回数
        if ($questlog->status !== $oldlog['status'] && $questlog->status === QuestStatus::SUCCEED) {
            foreach (UserAchievement::findActiveOrNew($questlog->user_id, AchievementCondition::ANY_QUESTS) as $userAchievement) {
                ++$userAchievement->score;
                $userAchievement->save();
            }
        }
    }

    /**
     * 購読するリスナの登録。
     * @param Dispatcher $events イベントディスパッチャー。
     */
    public function subscribe(Dispatcher $events) : void
    {
        $events->listen(
            QuestlogSaved::class,
            'App\Listeners\AchievementEventSubscriber@onQuestlogSaved'
        );
    }
}
