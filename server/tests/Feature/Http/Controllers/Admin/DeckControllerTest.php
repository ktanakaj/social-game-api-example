<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use App\Models\Globals\User;

class DeckControllerTest extends TestCase
{
    /**
     * デッキ一覧のテスト。
     */
    public function testIndex() : void
    {
        $user = factory(User::class)->create();
        $userDeck = $user->decks[0];

        $response = $this->withAdminLogin()->json('GET', "/admin/users/{$user->id}/decks");
        $response->assertStatus(200);

        $array = $response->json();
        $this->assertIsArray($array);
        $this->assertGreaterThan(0, count($array));

        $json = $array[0];
        $this->assertArrayHasKey('id', $json);
        $this->assertSame(1, $json['no']);
        $this->assertIsArray($json['cards']);
        $this->assertGreaterThan(0, count($json['cards']));
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $card = $json['cards'][0];
        $this->assertSame($userDeck->cards[0]->user_card_id, $card['userCardId']);
        $this->assertSame($userDeck->cards[0]->position, $card['position']);
    }
}
