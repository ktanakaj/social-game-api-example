<?php

namespace Tests\Feature\Http\Controllers;

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

        $response = $this->withLogin($user)->json('GET', "/decks");
        $response->assertStatus(200);

        $array = $response->json();
        $this->assertIsArray($array);
        $this->assertGreaterThan(0, count($array));

        $json = $array[0];
        $this->assertArrayHasKey('id', $json);
        $this->assertSame($userDeck->no, $json['no']);
        $this->assertIsArray($json['cards']);
        $this->assertGreaterThan(0, count($json['cards']));
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $card = $json['cards'][0];
        $this->assertSame($userDeck->cards[0]->user_card_id, $card['userCardId']);
        $this->assertSame($userDeck->cards[0]->position, $card['position']);
    }
    
    /**
     * デッキ付与のテスト。
     */
    public function testStore() : void
    {
        $user = factory(User::class)->states('allcards')->create();
        $userCard = $user->cards[0];
        $body = [
            [
                'userCardId' => $userCard->id,
                'position' => 1,
            ],
        ];

        // デッキを作成
        $response = $this->withLogin($user)->json('POST', "/decks", $body);
        $response
            ->assertStatus(201)
            ->assertJson([
                'no' => 2,
                'cards' => $body,
            ]);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseHas('user_decks', [
            'id' => $json['id'],
            'user_id' => $user->id,
            'no' => $json['no'],
        ]);
        $this->assertDatabaseHas('user_deck_cards', [
            'user_deck_id' => $json['id'],
            'user_card_id' => $userCard->id,
            'position' => 1,
        ]);
    }

    /**
     * デッキ更新のテスト。
     */
    public function testUpdate() : void
    {
        $user = factory(User::class)->states('allcards')->create();
        $userCard = $user->cards[0];
        $userCard2 = $user->cards[1];
        $userDeck = $user->decks[0];

        $body = [
            [
                'userCardId' => $userCard2->id,
                'position' => 2,
            ],
        ];
        $response = $this->withLogin($user)->json('PUT', "/decks/{$userDeck->id}", $body);
        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $userDeck->id,
                'no' => $userDeck->no,
                'cards' => $body,
            ]);

        $json = $response->json();
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);
        $this->assertCount(1, $json['cards']);

        $this->assertDatabaseHas('user_deck_cards', [
            'user_deck_id' => $userDeck->id,
            'user_card_id' => $userCard2->id,
            'position' => 2,
        ]);
        $this->assertDatabaseMissing('user_deck_cards', [
            'user_deck_id' => $userDeck->id,
            'user_card_id' => $userCard->id,
        ]);
    }

    /**
     * デッキ削除のテスト。
     */
    public function testDestroy() : void
    {
        $user = factory(User::class)->create();
        $userDeck = $user->decks[0];

        $response = $this->withLogin($user)->json('DELETE', "/decks/{$userDeck->id}");
        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $userDeck->id,
                'no' => $userDeck->no,
                'cards' => [
                    [
                        'userCardId' => $userDeck->cards[0]->user_card_id,
                        'position' => $userDeck->cards[0]->position,
                    ],
                ],
            ]);

        $json = $response->json();
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseMissing('user_decks', [
            'id' => $userDeck->id,
        ]);
    }
}
