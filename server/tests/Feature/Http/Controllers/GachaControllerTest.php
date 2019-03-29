<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Enums\ObjectType;
use App\Models\Globals\Gachalog;
use App\Models\Globals\User;

class GachaControllerTest extends TestCase
{
    /**
     * ガチャ一覧のテスト。
     */
    public function testIndex() : void
    {
        $user = factory(User::class)->create();

        // 有効なガチャの一覧が返る
        $response = $this->withLogin($user)->json('GET', '/gachas');
        $response
            ->assertStatus(200);

        $json = $response->json();
        $this->assertGreaterThan(0, count($json));

        $gacha = $json[0];
        $this->assertArrayHasKey('id', $gacha);
        $this->assertArrayHasKey('nameTextId', $gacha);
        $this->assertArrayHasKey('descTextId', $gacha);
        $this->assertArrayNotHasKey('drops', $gacha);
        $this->assertGreaterThan(0, count($gacha['prices']));

        $price = $gacha['prices'][0];
        $this->assertArrayHasKey('id', $price);
        $this->assertArrayHasKey('gachaId', $price);
        $this->assertArrayHasKey('objectType', $price);
        $this->assertArrayHasKey('objectId', $price);
        $this->assertArrayHasKey('prices', $price);
        $this->assertArrayHasKey('times', $price);
        $this->assertArrayHasKey('openAt', $price);
        $this->assertArrayHasKey('closeAt', $price);
    }

    /**
     * ガチャ詳細のテスト。
     */
    public function testShow() : void
    {
        // ガチャの確率などが返る
        $response = $this->withLogin()->json('GET', '/gachas/2');
        $response
            ->assertStatus(200);

        $json = $response->json();
        $this->assertSame(2, $json['id']);
        $this->assertArrayHasKey('nameTextId', $json);
        $this->assertArrayHasKey('descTextId', $json);
        $this->assertGreaterThan(0, count($json['prices']));
        $this->assertGreaterThan(0, count($json['drops']));

        $price = $json['prices'][0];
        $this->assertArrayHasKey('id', $price);
        $this->assertArrayHasKey('gachaId', $price);
        $this->assertArrayHasKey('objectType', $price);
        $this->assertArrayHasKey('objectId', $price);
        $this->assertArrayHasKey('prices', $price);
        $this->assertArrayHasKey('times', $price);
        $this->assertArrayHasKey('openAt', $price);
        $this->assertArrayHasKey('closeAt', $price);

        $drop = $json['drops'][0];
        $this->assertArrayHasKey('id', $drop);
        $this->assertArrayHasKey('gachaId', $drop);
        $this->assertArrayHasKey('objectType', $drop);
        $this->assertArrayHasKey('objectId', $drop);
        $this->assertArrayHasKey('count', $drop);
        $this->assertArrayHasKey('rate', $drop);
        $this->assertArrayHasKey('openAt', $drop);
        $this->assertArrayHasKey('closeAt', $drop);
        $this->assertArrayNotHasKey('weight', $drop);
    }

    /**
     * ガチャ抽選のテスト。
     */
    public function testLot() : void
    {
        $user = factory(User::class)->create();

        // レアガチャのテスト
        $response = $this->withLogin($user)->json('POST', '/gachas', [
            'gachaPriceId' => 101,
            'count' => 1,
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertCount(1, $json);

        $received = $json[0];
        $this->assertSame('card', $received['type']);
        $this->assertArrayHasKey('id', $received);
        $this->assertSame(1, $received['count']);
        $this->assertArrayHasKey('total', $received);
        $this->assertArrayHasKey('isNew', $received);

        $this->assertDatabaseHas('user_cards', [
            'user_id' => $user->id,
            'card_id' => $received['id'],
        ]);
    }

    /**
     * ガチャ履歴のテスト。
     */
    public function testLogs() : void
    {
        $user = factory(User::class)->create();
        factory(Gachalog::class)->create(['user_id' => $user->id]);

        // ページング条件なしで取得
        $response = $this->withLogin($user)->json('GET', "/gachas/logs");
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
        $this->assertArrayHasKey('gachaId', $log);
        $this->assertArrayHasKey('gachaPriceId', $log);
        $this->assertArrayHasKey('createdAt', $log);

        $this->assertGreaterThan(0, count($log['drops']));
        $dropped = $log['drops'][0];
        $this->assertArrayHasKey('objectType', $dropped);
        $this->assertArrayHasKey('objectId', $dropped);
        $this->assertArrayHasKey('count', $dropped);
        $this->assertArrayNotHasKey('id', $dropped);
        $this->assertArrayNotHasKey('gachalogId', $dropped);
        $this->assertArrayNotHasKey('createdAt', $dropped);
    }
}
