<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Globals\User;
use App\Models\Globals\UserAchievement;

class AchievementControllerTest extends TestCase
{
    /**
     * アチーブメント達成状況一覧のテスト。
     */
    public function testIndex() : void
    {
        $user = factory(User::class)->create();

        $response = $this->withLogin($user)->json('GET', '/achievements');
        $response->assertStatus(200);

        $array = $response->json();
        $this->assertIsArray($array);
        $this->assertGreaterThan(0, count($array));

        $userAchievement = $array[0];
        $this->assertArrayHasKey('id', $userAchievement);
        $this->assertArrayHasKey('achievementId', $userAchievement);
        $this->assertArrayHasKey('score', $userAchievement);
        $this->assertArrayHasKey('received', $userAchievement);
        $this->assertArrayHasKey('createdAt', $userAchievement);
        $this->assertArrayHasKey('updatedAt', $userAchievement);

        // 自動更新されたレコードも含まれること
        // （id=1はレベル5アチーブメント）
        $userAchievement = null;
        foreach ($array as $m) {
            if ($m['achievementId'] === 1) {
                $userAchievement = $m;
            }
        }
        $this->assertNotNull($userAchievement);
        $this->assertTrue($user->level === $userAchievement['score'] || 5 === $userAchievement['score']);

        // TODO: デイリー/ウィークリーの旧データ削除も確認する
    }

    /**
     * アチーブメント報酬受取のテスト。
     */
    public function testReceive() : void
    {
        $user = factory(User::class)->create();
        $userAchievement = factory(UserAchievement::class)->states('level5')->make();
        $user->achievements()->save($userAchievement);
        $response = $this->withLogin($user)->json('POST', "/achievements/{$userAchievement->id}/recv");
        $response
            ->assertStatus(200)
            ->assertJson([
                'type' => 'gameCoin',
                'id' => null,
                'count' => $userAchievement->achievement->count,
                'total' => $user->game_coins + $userAchievement->achievement->count,
                'isNew' => false,
            ]);

        $this->assertDatabaseHas('user_achievements', [
            'id' => $userAchievement->id,
            'received' => 1,
        ]);
    }

    /**
     * 全アチーブメント報酬受取のテスト。
     */
    public function testReceiveAll() : void
    {
        // ※ この他に、factoryのランダムデータでデイリーやウィークリーも達成済みになることがある
        $user = factory(User::class)->create();
        $userAchievement1 = factory(UserAchievement::class)->states('level5')->make();
        $userAchievement2 = factory(UserAchievement::class)->states('level10')->make();
        $user->achievements()->saveMany([$userAchievement1, $userAchievement2]);

        $response = $this->withLogin($user)->json('POST', '/achievements/recv');
        $response->assertStatus(200);

        $array = $response->json();
        $this->assertGreaterThanOrEqual(2, count($array));

        $received = $array[0];
        $this->assertSame('gameCoin', $received['type']);
        $this->assertNull($received['id']);
        $this->assertSame(500, $received['count']);
        $this->assertFalse($received['isNew']);

        $received = $array[1];
        $this->assertSame('gameCoin', $received['type']);
        $this->assertNull($received['id']);
        $this->assertSame(1000, $received['count']);
        $this->assertFalse($received['isNew']);

        $afterUser = User::findOrFail($user->id);
        $this->assertGreaterThanOrEqual($user->game_coins + 1500, $afterUser->game_coins);

        $this->assertDatabaseHas('user_achievements', [
            'id' => $userAchievement1->id,
            'received' => 1,
        ]);
        $this->assertDatabaseHas('user_achievements', [
            'id' => $userAchievement2->id,
            'received' => 1,
        ]);
    }
}
