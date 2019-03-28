<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Globals\User;

class QuestControllerTest extends TestCase
{
    /**
     * クエスト一覧のテスト。
     */
    public function testIndex() : void
    {
        // ページング条件なしで取得
        $response = $this->withLogin()->json('GET', '/quests');
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
}
