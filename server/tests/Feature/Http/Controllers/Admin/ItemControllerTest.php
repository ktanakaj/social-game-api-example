<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    /**
     * アイテム一覧のテスト。
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

        // TODO: 0個のアイテムが参照されないこともテストする
    }


    /**
     * アイテム付与のテスト。
     */
    public function testStore() : void
    {
        // ユーザーを作成、アイテムを付与
        $user = $this->createTestUser();
        $body = [
            'itemId' => 100,
            'count' => 1,
        ];
        $response = $this->withAdminLogin()->json('POST', "/admin/users/{$user->id}/items", $body);
        $response
            ->assertStatus(201)
            ->assertJson($body);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseHas('user_items', [
            'id' => $json['id'],
            'user_id' => $user->id,
            'item_id' => $json['itemId'],
            'count' => $json['count'],
        ]);
    }

    /**
     * アイテム更新のテスト。
     */
    public function testUpdate() : void
    {
        // 一人ユーザーを作成、アイテムを付与
        $user = $this->createTestUser();
        $userItem = $user->items()->create([
            'item_id' => 100,
        ]);

        // アイテムを更新
        $body = [
            'count' => 2,
        ];
        $response = $this->withAdminLogin()->json('PUT', "/admin/users/{$user->id}/items/{$userItem->id}", $body);
        $response
            ->assertStatus(200)
            ->assertJson($body);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertSame($userItem->item_id, $json['itemId']);
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseHas('user_items', [
            'id' => $json['id'],
            'user_id' => $user->id,
            'item_id' => $json['itemId'],
            'count' => $json['count'],
        ]);
    }

    /**
     * アイテム削除のテスト。
     */
    public function testDestroy() : void
    {
        // 一人ユーザーを作成、アイテムを付与
        $user = $this->createTestUser();
        $userItem = $user->items()->create([
            'item_id' => 100,
            'count' => 1,
        ]);

        // アイテムを削除
        $response = $this->withAdminLogin()->json('DELETE', "/admin/users/{$user->id}/items/{$userItem->id}");
        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $userItem->id,
                'itemId' => $userItem->item_id,
                'count' => 0,
            ]);

        $json = $response->json();
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        // DB上は個数が0に更新される
        $this->assertDatabaseHas('user_items', [
            'id' => $userItem->id,
            'item_id' => $userItem->item_id,
            'count' => 0,
        ]);
    }
}
