<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Globals\UserDeck;

class DeckControllerTest extends TestCase
{
    /**
     * デッキ一覧のテスト。
     */
    public function testIndex() : void
    {
        // テストデータを作って、それを表示
        $user = $this->createTestUser();
        $userCard = $user->cards()->create([
            'card_id' => 1000,
        ]);
        $userDeck = new UserDeck();
        $userDeck->no = 1;
        $user->decks()->save($userDeck);
        $userDeck->cards()->create(['userCardId' => $userCard->id, 'position' => 1]);

        $response = $this->withLogin($user)->json('GET', "/decks");
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
    
    /**
     * デッキ付与のテスト。
     */
    public function testStore() : void
    {
        // ユーザーを作成、カードを付与
        $user = $this->createTestUser();
        $userCard = $user->cards()->create([
            'card_id' => 1000,
        ]);
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
                'no' => 1,
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
        // テストデータを作って、それを更新
        $user = $this->createTestUser();
        $userCard = $user->cards()->create([
            'card_id' => 1000,
        ]);
        $userCard2 = $user->cards()->create([
            'card_id' => 2000,
        ]);
        $userDeck = new UserDeck();
        $userDeck->no = 1;
        $user->decks()->save($userDeck);
        $userDeck->cards()->create(['userCardId' => $userCard->id, 'position' => 1]);

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
        // テストデータを作って、それを削除
        $user = $this->createTestUser();
        $userCard = $user->cards()->create([
            'card_id' => 1000,
        ]);
        $userDeck = new UserDeck();
        $userDeck->no = 1;
        $user->decks()->save($userDeck);
        $userDeck->cards()->create(['userCardId' => $userCard->id, 'position' => 1]);

        $response = $this->withLogin($user)->json('DELETE', "/decks/{$userDeck->id}");
        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $userDeck->id,
                'no' => $userDeck->no,
                'cards' => [
                    [
                        'userCardId' => $userCard->id,
                        'position' => 1,
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
