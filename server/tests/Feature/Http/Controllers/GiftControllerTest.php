<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Globals\User;
use App\Models\Globals\UserGift;

class GiftControllerTest extends TestCase
{
    /**
     * ギフト一覧のテスト。
     */
    public function testIndex() : void
    {
        $user = factory(User::class)->states('allgifts')->create();

        // ページング条件なしで取得
        $response = $this->withLogin($user)->json('GET', '/gifts');
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
     * ギフト受取のテスト。
     */
    public function testReceive() : void
    {
        // ※ プレイヤーはid=2200のカードは持っていない想定
        $user = factory(User::class)->create();
        $userGift = factory(UserGift::class)->states('card')->make([
            'object_id' => 2200,
        ]);
        $user->gifts()->save($userGift);
        $response = $this->withLogin($user)->json('POST', "/gifts/{$userGift->id}/recv");
        $response
            ->assertStatus(200)
            ->assertJson([
                'type' => 'card',
                'id' => $userGift->object_id,
                'count' => $userGift->count,
                'isNew' => true,
            ]);

        $this->assertDatabaseHas('user_cards', [
            'user_id' => $user->id,
            'card_id' => $userGift->object_id,
            'count' => 1,
        ]);
        $this->assertSoftDeleted('user_gifts', [
            'id' => $userGift->id,
        ]);
    }

    /**
     * 全ギフト受取のテスト。
     */
    public function testReceiveAll() : void
    {
        // ※ プレイヤーはid=2200のカードは持っていない想定
        $user = factory(User::class)->create();
        $userGift1 = factory(UserGift::class)->states('card')->make([
            'object_id' => 2200,
        ]);
        $userGift2 = factory(UserGift::class)->states('gameCoin')->make();
        $user->gifts()->saveMany([$userGift1, $userGift2]);

        $response = $this->withLogin($user)->json('POST', '/gifts/recv');
        $response->assertStatus(200);

        $array = $response->json();
        $this->assertGreaterThanOrEqual(2, count($array));

        $received = $array[0];
        $this->assertSame('card', $received['type']);
        $this->assertSame($userGift1->object_id, $received['id']);
        $this->assertSame($userGift1->count, $received['count']);
        $this->assertTrue($received['isNew']);

        $received = $array[1];
        $this->assertSame('gameCoin', $received['type']);
        $this->assertNull($received['id']);
        $this->assertSame($userGift2->count, $received['count']);
        $this->assertFalse($received['isNew']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'game_coins' => $user->game_coins + $userGift2->count,
        ]);
        $this->assertDatabaseHas('user_cards', [
            'user_id' => $user->id,
            'card_id' => $userGift1->object_id,
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
