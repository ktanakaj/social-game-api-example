<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Globals\UserGift;

class GiftControllerTest extends TestCase
{
    /**
     * ユーザーギフト一覧のテスト。
     */
    public function testIndex() : void
    {
        // テストデータを作って、それを表示
        $user = $this->createTestUser();
        $user->gifts()->create([
            'text_id' => 'GIFT_MESSAGE_COVERING',
            'object_type' => 'item',
            'object_id' => 100,
        ]);

        // ページング条件なしで取得
        $response = $this->withLogin($user)->json('GET', "/gifts");
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
     * ユーザーギフト受取のテスト。
     */
    public function testReceive() : void
    {
        // テストデータを作って、それを受け取る
        $user = $this->createTestUser();
        $userGift = $user->gifts()->create([
            'text_id' => 'GIFT_MESSAGE_COVERING',
            'object_type' => 'item',
            'object_id' => 100,
        ]);
        $response = $this->withLogin($user)->json('POST', "/gifts/{$userGift->id}/recv");
        $response
            ->assertStatus(200)
            ->assertJson([
                'object_type' => 'item',
                'object_id' => 100,
                'count' => 1,
                'is_new' => true,
            ]);

        $this->assertDatabaseHas('user_items', [
            'user_id' => $user->id,
            'item_id' => 100,
            'count' => 1,
        ]);
        $this->assertSoftDeleted('user_gifts', [
            'id' => $userGift->id,
        ]);
    }

    /**
     * 全ユーザーギフト受取のテスト。
     */
    public function testReceiveAll() : void
    {
        // テストデータを作って、それを受け取る
        $user = $this->createTestUser();
        $userGift1 = $user->gifts()->create([
            'text_id' => 'GIFT_MESSAGE_COVERING',
            'object_type' => 'item',
            'object_id' => 100,
        ]);
        $userGift2 = $user->gifts()->create([
            'text_id' => 'GIFT_MESSAGE_COVERING',
            'object_type' => 'gameCoin',
            'count' => 10000,
        ]);
        $response = $this->withLogin($user)->json('POST', '/gifts/recv');
        $response->assertStatus(200);

        $json = $response->json();

        $this->assertGreaterThan(1, count($json));

        $received = $json[0];
        $this->assertEquals('item', $received['object_type']);
        $this->assertEquals(100, $received['object_id']);
        $this->assertEquals(1, $received['count']);
        $this->assertTrue($received['is_new']);

        $received = $json[1];
        $this->assertEquals('gameCoin', $received['object_type']);
        $this->assertNull($received['object_id']);
        $this->assertEquals(10000, $received['count']);
        $this->assertFalse($received['is_new']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'game_coins' => $user->game_coins + 10000,
        ]);
        $this->assertDatabaseHas('user_items', [
            'user_id' => $user->id,
            'item_id' => 100,
            'count' => 1,
        ]);
        $this->assertSoftDeleted('user_gifts', [
            'id' => $userGift1->id,
        ]);
        $this->assertSoftDeleted('user_gifts', [
            'id' => $userGift2->id,
        ]);
    }
}
