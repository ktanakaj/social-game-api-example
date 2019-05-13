<?php

namespace Tests\Unit\Models\Globals;

use Tests\TestCase;
use Carbon\Carbon;
use App\Models\Globals\User;
use App\Models\Globals\UserAchievement;
use App\Models\Masters\Achievement;

class UserAchievementTest extends TestCase
{
    /**
     * アチーブメントは期限切れかのテスト。
     */
    public function testIsExpired() : void
    {
        // 通常、無期限
        $userAchievement = new UserAchievement(['achievement_id' => 1]);
        $this->assertFalse($userAchievement->isExpired());
        $userAchievement->updated_at = Carbon::now();
        $this->assertFalse($userAchievement->isExpired());

        // デイリー
        $userAchievement = new UserAchievement(['achievement_id' => 10000]);
        $this->assertFalse($userAchievement->isExpired());
        $userAchievement->updated_at = Carbon::now();
        $this->assertFalse($userAchievement->isExpired());
        $this->setTestNow(Carbon::tomorrow());
        $this->assertTrue($userAchievement->isExpired());

        // ウィークリー
        $userAchievement = new UserAchievement(['achievement_id' => 20000]);
        $this->assertFalse($userAchievement->isExpired());
        $userAchievement->updated_at = Carbon::now();
        $this->assertFalse($userAchievement->isExpired());
        $this->setTestNow(Carbon::now()->endOfWeek());
        $this->assertFalse($userAchievement->isExpired());
        $this->setTestNow(Carbon::now()->next(Carbon::MONDAY));
        $this->assertTrue($userAchievement->isExpired());

        // TODO: openAt, closeAtがあるマスタを作ってそれもテストする
    }

    /**
     * 現在有効なアチーブメント一覧を取得のテスト。
     */
    public function testFindActiveOrNew() : void
    {
        $user = factory(User::class)->create();

        // 有効なマスタ分のデータが全て返る
        $userAchievements = UserAchievement::findActiveOrNew($user->id);
        $this->assertSame(Achievement::active()->count(), $userAchievements->count());

        // デイリーのマスタをチェック（ランダムなユーザーデータとして作られている可能性有）
        $userAchievementDaily = $userAchievements->where('achievement_id', 10000)->first();
        $this->assertNotNull($userAchievementDaily);
        $userAchievementDaily->score = 10;
        $userAchievementDaily->received = true;
        $userAchievementDaily->save();

        // 再検索。先ほど更新したデータが取れる
        $userAchievements = UserAchievement::findActiveOrNew($user->id);
        $this->assertSame(Achievement::active()->count(), $userAchievements->count());
        $userAchievementDaily = $userAchievements->where('achievement_id', 10000)->first();
        $this->assertNotNull($userAchievementDaily);
        $this->assertSame(10, $userAchievementDaily->score);
        $this->assertTrue($userAchievementDaily->received);

        // 日にちを変更。デイリーのアチーブメントはリセットされている
        $this->setTestNow(Carbon::tomorrow());
        $userAchievements = UserAchievement::findActiveOrNew($user->id);
        $this->assertSame(Achievement::active()->count(), $userAchievements->count());
        $userAchievementDaily = $userAchievements->where('achievement_id', 10000)->first();
        $this->assertNotNull($userAchievementDaily);
        $this->assertSame(0, $userAchievementDaily->score);
        $this->assertFalse($userAchievementDaily->received);

        // TODO: 検索条件を指定してのテストも足す
    }
}
