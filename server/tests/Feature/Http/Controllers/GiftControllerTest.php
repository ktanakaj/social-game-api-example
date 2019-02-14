<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Globals\UserGift;

class GiftControllerTest extends TestCase
{
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
            ]);

        // TODO: DBもチェックする
    }

    /**
     * 全ユーザーギフト受取のテスト。
     */
    public function testAllReceive() : void
    {
        // テストデータを作って、それを受け取る
        $user = $this->createTestUser();
        $user->gifts()->create([
            'text_id' => 'GIFT_MESSAGE_COVERING',
            'object_type' => 'item',
            'object_id' => 100,
        ]);
        $user->gifts()->create([
            'text_id' => 'GIFT_MESSAGE_COVERING',
            'object_type' => 'gameCoin',
            'count' => 10000,
        ]);
        $response = $this->withLogin($user)->json('POST', '/gifts/recv');
        $response->assertStatus(200);

        $json = $response->json();

        $this->assertGreaterThan(1, count($json));
        // TODO: 配列の中身もチェックする
        // TODO: DBもチェックする
    }
}
