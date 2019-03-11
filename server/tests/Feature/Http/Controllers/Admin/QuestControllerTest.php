<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use App\Models\Globals\User;
use App\Models\Globals\Questlog;

class QuestControllerTest extends TestCase
{
    /**
     * クエスト一覧のテスト。
     */
    public function testIndex() : void
    {
        $user = factory(User::class)->create();

        // ページング条件なしで取得
        $response = $this->withAdminLogin()->json('GET', "/admin/users/{$user->id}/quests");
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

        $userQuest = $json['data'][0];
        $this->assertArrayHasKey('id', $userQuest);
        $this->assertArrayHasKey('questId', $userQuest);
        $this->assertArrayHasKey('count', $userQuest);
        $this->assertArrayHasKey('createdAt', $userQuest);
        $this->assertArrayHasKey('updatedAt', $userQuest);
    }

    /**
     * クエスト達成のテスト。
     */
    public function testStore() : void
    {
        $user = factory(User::class)->create();
        $body = [
            'questId' => 2,
            'count' => 1,
        ];
        $response = $this->withAdminLogin()->json('POST', "/admin/users/{$user->id}/quests", $body);
        $response
            ->assertStatus(201)
            ->assertJson($body);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseHas('user_quests', [
            'id' => $json['id'],
            'user_id' => $user->id,
            'quest_id' => $json['questId'],
            'count' => $json['count'],
        ]);
    }

    /**
     * クエスト削除のテスト。
     */
    public function testDestroy() : void
    {
        $user = factory(User::class)->create();
        $userQuest = $user->quests[0];

        // クエストを削除
        $response = $this->withAdminLogin()->json('DELETE', "/admin/users/{$user->id}/quests/{$userQuest->id}");
        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $userQuest->id,
                'questId' => $userQuest->quest_id,
                'count' => $userQuest->count,
            ]);

        $json = $response->json();
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseMissing('user_quests', [
            'id' => $userQuest->id,
        ]);
    }

    /**
     * クエスト履歴のテスト。
     */
    public function testLogs() : void
    {
        $user = factory(User::class)->create();
        $user->questlogs()->save(factory(Questlog::class)->make());

        // ページング条件なしで取得
        $response = $this->withAdminLogin()->json('GET', "/admin/users/{$user->id}/quests/logs");
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

        $log = $json['data'][0];
        $this->assertArrayHasKey('id', $log);
        $this->assertArrayHasKey('userId', $log);
        $this->assertArrayHasKey('questId', $log);
        $this->assertArrayHasKey('status', $log);
        $this->assertArrayHasKey('createdAt', $log);
        $this->assertArrayHasKey('updatedAt', $log);
    }
}
