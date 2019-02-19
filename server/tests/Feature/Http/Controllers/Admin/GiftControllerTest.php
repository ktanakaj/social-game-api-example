<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;

class GiftControllerTest extends TestCase
{
    /**
     * ギフト一覧のテスト。
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
                'perPage' => 20,
                'currentPage' => 1,
                'from' => 1,
            ]);

        $json = $response->json();
        $this->assertGreaterThan(0, $json['total']);
        $this->assertGreaterThan(0, $json['lastPage']);
        $this->assertGreaterThan(0, $json['to']);
        $this->assertGreaterThan(0, count($json['data']));

        $userGift = $json['data'][0];
        $this->assertArrayHasKey('id', $userGift);
        $this->assertArrayHasKey('textId', $userGift);
        $this->assertArrayHasKey('textOptions', $userGift);
        $this->assertArrayHasKey('objectType', $userGift);
        $this->assertArrayHasKey('objectId', $userGift);
        $this->assertArrayHasKey('count', $userGift);
        $this->assertArrayHasKey('createdAt', $userGift);
    }

    /**
     * ギフト付与のテスト。
     */
    public function testStore() : void
    {
        // ユーザーを作成、ギフトを付与
        $user = $this->createTestUser();
        $body = [
            'textId' => 'GIFT_MESSAGE_COVERING',
            'objectType' => 'item',
            'objectId' => 100,
        ];
        $response = $this->withAdminLogin()->json('POST', "/admin/users/{$user->id}/gifts", $body);
        $response
            ->assertStatus(201)
            ->assertJson($body);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('createdAt', $json);

        $this->assertDatabaseHas('user_gifts', [
            'id' => $json['id'],
            'user_id' => $user->id,
            'text_id' => 'GIFT_MESSAGE_COVERING',
            'object_type' => 'item',
            'object_id' => 100,
        ]);
    }

    /**
     * ギフト削除のテスト。
     */
    public function testDestroy() : void
    {
        // 一人ユーザーを作成、ギフトを付与
        $user = $this->createTestUser();
        $userGift = $user->gifts()->create([
            'text_id' => 'GIFT_MESSAGE_COVERING',
            'object_type' => 'item',
            'object_id' => 100,
        ]);

        // ギフトを削除
        $response = $this->withAdminLogin()->json('DELETE', "/admin/users/{$user->id}/gifts/{$userGift->id}");
        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $userGift->id,
                'textId' => $userGift->text_id,
                'objectType' => $userGift->object_type,
                'objectId' => $userGift->object_id,
                'count' => 1,
            ]);

        $json = $response->json();
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('deletedAt', $json);

        $this->assertSoftDeleted('user_gifts', [
            'id' => $userGift->id,
        ]);
    }
}
