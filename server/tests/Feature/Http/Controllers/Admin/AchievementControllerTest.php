<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use App\Models\Globals\User;

class AchievementControllerTest extends TestCase
{
    /**
     * アチーブメント達成状況一覧のテスト。
     */
    public function testIndex() : void
    {
        $user = factory(User::class)->create();

        // ページング条件なしで取得
        $response = $this->withAdminLogin()->json('GET', "/admin/users/{$user->id}/achievements");
        $response
            ->assertStatus(200)
            ->assertJson([
                'perPage' => 20,
                'currentPage' => 1,
                'from' => 1,
            ]);

        $json = $response->json();
        $this->assertGreaterThan(0, $json['total']);
        $this->assertGreaterThan(0, $json['lastPage']);
        $this->assertGreaterThan(0, $json['to']);
        $this->assertGreaterThan(0, count($json['data']));

        $userAchievement = $json['data'][0];
        $this->assertArrayHasKey('id', $userAchievement);
        $this->assertArrayHasKey('achievementId', $userAchievement);
        $this->assertArrayHasKey('score', $userAchievement);
        $this->assertArrayHasKey('received', $userAchievement);
        $this->assertArrayHasKey('createdAt', $userAchievement);
        $this->assertArrayHasKey('updatedAt', $userAchievement);
    }
}
