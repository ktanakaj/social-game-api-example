<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;

class GiftControllerTest extends TestCase
{
    /**
     * ユーザーギフト一覧のテスト。
     */
    public function testIndex() : void
    {
        // 一人ユーザーを作成、ギフトを付与
        $user = $this->createTestUser();
        $user->gifts()->create([
            'text_id' => 'GIFT_MESSAGE_COVERING',
            'object_type' => 'item',
            'object_id' => 100,
        ]);

        // ページング条件なしで取得
        $response = $this->withAdminLogin()->json('GET', "/admin/users/{$user->id}/gifts");
        $response
            ->assertStatus(200)
            ->assertJson([
                'per_page' => 20,
                'current_page' => 1,
                'from' => 1,
            ]);

        $json = $response->json();
        $this->assertGreaterThan(0, $json['total']);
        $this->assertGreaterThan(0, $json['last_page']);
        $this->assertGreaterThan(0, $json['to']);
        $this->assertGreaterThan(0, count($json['data']));

        $userGift = $json['data'][0];
        $this->assertArrayHasKey('id', $userGift);
        $this->assertArrayHasKey('text_id', $userGift);
        $this->assertArrayHasKey('text_options', $userGift);
        $this->assertArrayHasKey('object_type', $userGift);
        $this->assertArrayHasKey('object_id', $userGift);
        $this->assertArrayHasKey('count', $userGift);
        $this->assertArrayHasKey('created_at', $userGift);
    }

    /**
     * ユーザーギフト付与のテスト。
     */
    public function testStore() : void
    {
        // ユーザーを作成、ギフトを付与
        $user = $this->createTestUser();
        $body = [
            'text_id' => 'GIFT_MESSAGE_COVERING',
            'object_type' => 'item',
            'object_id' => 100,
        ];
        $response = $this->withAdminLogin()->json('POST', "/admin/users/{$user->id}/gifts", $body);
        $response
            ->assertStatus(201)
            ->assertJson($body);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('created_at', $json);

        $this->assertDatabaseHas('user_gifts', [
            'id' => $json['id'],
            'user_id' => $user->id,
        ] + $body);
    }
}
