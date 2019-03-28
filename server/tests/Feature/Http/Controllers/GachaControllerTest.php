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

        // ユーザーが実施可能なガチャの一覧が返る
        $response = $this->withLogin($user)->json('GET', '/gachas');
        $response
            ->assertStatus(200);

        $json = $response->json();
        // TODO: 未実装
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
        // TODO: 未実装
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
        $user->gachalogs()->save(factory(Gachalog::class)->make());

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

        // TODO: 未実装
    }
}
