<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;

class CardControllerTest extends TestCase
{
    /**
     * カード一覧のテスト。
     */
    public function testIndex() : void
    {
        // 一人ユーザーを作成、カードを付与
        $user = $this->createTestUser();
        $user->cards()->create([
            'card_id' => 1000,
        ]);

        // ページング条件なしで取得
        $response = $this->withAdminLogin()->json('GET', "/admin/users/{$user->id}/cards");
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

        $userCard = $json['data'][0];
        $this->assertArrayHasKey('id', $userCard);
        $this->assertArrayHasKey('cardId', $userCard);
        $this->assertArrayHasKey('exp', $userCard);
        $this->assertArrayHasKey('count', $userCard);
        $this->assertArrayHasKey('createdAt', $userCard);
        $this->assertArrayHasKey('updatedAt', $userCard);
    }

    /**
     * カード付与のテスト。
     */
    public function testStore() : void
    {
        // ユーザーを作成、カードを付与
        $user = $this->createTestUser();
        $body = [
            'cardId' => 1000,
        ];
        $response = $this->withAdminLogin()->json('POST', "/admin/users/{$user->id}/cards", $body);
        $response
            ->assertStatus(201)
            ->assertJson($body);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertSame(0, $json['exp']);
        $this->assertSame(1, $json['count']);
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseHas('user_cards', [
            'id' => $json['id'],
            'user_id' => $user->id,
            'card_id' => $json['cardId'],
            'exp' => $json['exp'],
            'count' => $json['count'],
        ]);
    }

    /**
     * カード更新のテスト。
     */
    public function testUpdate() : void
    {
        // 一人ユーザーを作成、カードを付与
        $user = $this->createTestUser();
        $userCard = $user->cards()->create([
            'card_id' => 1000,
        ]);

        // カードを更新
        $body = [
            'exp' => 1000,
            'count' => 2,
        ];
        $response = $this->withAdminLogin()->json('PUT', "/admin/users/{$user->id}/cards/{$userCard->id}", $body);
        $response
            ->assertStatus(200)
            ->assertJson($body);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertSame($userCard->card_id, $json['cardId']);
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseHas('user_cards', [
            'id' => $json['id'],
            'user_id' => $user->id,
            'card_id' => $json['cardId'],
            'exp' => $json['exp'],
            'count' => $json['count'],
        ]);
    }

    /**
     * カード削除のテスト。
     */
    public function testDestroy() : void
    {
        // 一人ユーザーを作成、カードを付与
        $user = $this->createTestUser();
        $userCard = $user->cards()->create([
            'card_id' => 1000,
        ]);

        // カードを削除
        $response = $this->withAdminLogin()->json('DELETE', "/admin/users/{$user->id}/cards/{$userCard->id}");
        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $userCard->id,
                'cardId' => $userCard->card_id,
                'exp' => $userCard->exp,
                'count' => 1,
            ]);

        $json = $response->json();
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseMissing('user_cards', [
            'id' => $userCard->id,
        ]);
    }
}
