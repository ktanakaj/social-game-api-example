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
                'objectType' => 'item',
                'objectId' => 100,
                'count' => 1,
                'isNew' => true,
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
        $this->assertEquals('item', $received['objectType']);
        $this->assertEquals(100, $received['objectId']);
        $this->assertEquals(1, $received['count']);
        $this->assertTrue($received['isNew']);

        $received = $json[1];
        $this->assertEquals('gameCoin', $received['objectType']);
        $this->assertNull($received['objectId']);
        $this->assertEquals(10000, $received['count']);
        $this->assertFalse($received['isNew']);

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
