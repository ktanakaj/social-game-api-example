<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use App\Models\Globals\UserDeck;

class DeckControllerTest extends TestCase
{
    /**
     * デッキ一覧のテスト。
     */
    public function testIndex() : void
    {
        // 一人ユーザーを作成、デッキを作成して検索
        $user = $this->createTestUser();
        $userCard = $user->cards()->create([
            'card_id' => 1000,
        ]);
        $userDeck = new UserDeck();
        $userDeck->no = 1;
        $user->decks()->save($userDeck);
        $userDeck->cards()->create(['userCardId' => $userCard->id, 'position' => 1]);

        $response = $this->withAdminLogin()->json('GET', "/admin/users/{$user->id}/decks");
        $response->assertStatus(200);

        $array = $response->json();
        $this->assertInternalType('array', $array);
        $this->assertGreaterThan(0, count($array));

        $json = $array[0];
        $this->assertArrayHasKey('id', $json);
        $this->assertSame(1, $json['no']);
        $this->assertInternalType('array', $json['cards']);
        $this->assertGreaterThan(0, count($json['cards']));
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $card = $json['cards'][0];
        $this->assertSame($userCard->id, $card['userCardId']);
        $this->assertSame(1, $card['position']);
    }
}
