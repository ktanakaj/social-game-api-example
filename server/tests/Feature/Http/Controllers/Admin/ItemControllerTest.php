<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    /**
     * ユーザーアイテム一覧のテスト。
     */
    public function testIndex() : void
    {
        // 一人ユーザーを作成、アイテムを付与
        $user = $this->createTestUser();
        $user->items()->create([
            'item_id' => 100,
            'count' => 1,
        ]);

        // ページング条件なしで取得
        $response = $this->withAdminLogin()->json('GET', "/admin/users/{$user->id}/items");
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

        $userItem = $json['data'][0];
        $this->assertArrayHasKey('id', $userItem);
        $this->assertArrayHasKey('itemId', $userItem);
        $this->assertArrayHasKey('count', $userItem);
        $this->assertArrayHasKey('createdAt', $userItem);
        $this->assertArrayHasKey('updatedAt', $userItem);
    }
}
