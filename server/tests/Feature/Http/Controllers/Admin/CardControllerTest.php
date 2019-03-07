<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use App\Models\Globals\User;

class CardControllerTest extends TestCase
{
    /**
     * カード一覧のテスト。
     */
    public function testIndex() : void
    {
        $user = factory(User::class)->states('allcards')->create();

        // ページング条件なしで取得
        $response = $this->withAdminLogin()->json('GET', "/admin/users/{$user->id}/cards");
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

        $userCard = $json['data'][0];
        $this->assertArrayHasKey('id', $userCard);
        $this->assertArrayHasKey('cardId', $userCard);
        $this->assertArrayHasKey('exp', $userCard);
        $this->assertArrayHasKey('count', $userCard);
        $this->assertArrayHasKey('createdAt', $userCard);
        $this->assertArrayHasKey('updatedAt', $userCard);
    }

    /**
     * カード付与のテスト。
     */
    public function testStore() : void
    {
        $user = factory(User::class)->create();
        $body = [
            'cardId' => 2000,
        ];
        $response = $this->withAdminLogin()->json('POST', "/admin/users/{$user->id}/cards", $body);
        $response
            ->assertStatus(201)
            ->assertJson($body);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertSame(0, $json['exp']);
        $this->assertSame(1, $json['count']);
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseHas('user_cards', [
            'id' => $json['id'],
            'user_id' => $user->id,
            'card_id' => $json['cardId'],
            'exp' => $json['exp'],
            'count' => $json['count'],
        ]);
    }

    /**
     * カード更新のテスト。
     */
    public function testUpdate() : void
    {
        $user = factory(User::class)->create();
        $userCard = $user->cards[0];

        // カードを更新
        $body = [
            'exp' => 1000,
            'count' => 2,
        ];
        $response = $this->withAdminLogin()->json('PUT', "/admin/users/{$user->id}/cards/{$userCard->id}", $body);
        $response
            ->assertStatus(200)
            ->assertJson($body);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertSame($userCard->card_id, $json['cardId']);
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseHas('user_cards', [
            'id' => $json['id'],
            'user_id' => $user->id,
            'card_id' => $json['cardId'],
            'exp' => $json['exp'],
            'count' => $json['count'],
        ]);
    }

    /**
     * カード削除のテスト。
     */
    public function testDestroy() : void
    {
        // デッキに入っていないカードを削除
        $user = factory(User::class)->states('allcards')->create();
        foreach ($user->cards as $c) {
            if (count($c->decks) === 0) {
                $userCard = $c;
                break;
            }
        }

        // カードを削除
        $response = $this->withAdminLogin()->json('DELETE', "/admin/users/{$user->id}/cards/{$userCard->id}");
        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $userCard->id,
                'cardId' => $userCard->card_id,
                'exp' => $userCard->exp,
                'count' => $userCard->count,
            ]);

        $json = $response->json();
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseMissing('user_cards', [
            'id' => $userCard->id,
        ]);
    }

    /**
     * カード削除のテスト（デッキ登録済）。
     */
    public function testDestroyWithDeck() : void
    {
        // デッキにカードが2枚入っている状態で、入っているカードを削除
        $user = factory(User::class)->create();
        $userDeck = $user->decks[0];
        $userCardId = $userDeck->cards[0]->user_card_id;
        $userCardId2 = $userDeck->cards[1]->user_card_id;
        $this->assertCount(2, $userDeck->cards);

        // カードを削除するとデッキからも取り除かれる
        $response = $this->withAdminLogin()->json('DELETE', "/admin/users/{$user->id}/cards/{$userCardId}");
        $response->assertStatus(200);

        $this->assertDatabaseMissing('user_cards', [
            'id' => $userCardId,
        ]);
        $this->assertDatabaseMissing('user_deck_cards', [
            'user_deck_id' => $userDeck->id,
            'user_card_id' => $userCardId,
        ]);
        $this->assertDatabaseHas('user_deck_cards', [
            'user_deck_id' => $userDeck->id,
            'user_card_id' => $userCardId2,
        ]);
        $this->assertDatabaseHas('user_decks', [
            'id' => $userDeck->id,
        ]);

        // デッキが空になるとデッキも消える
        $response = $this->withAdminLogin()->json('DELETE', "/admin/users/{$user->id}/cards/{$userCardId2}");
        $response->assertStatus(200);

        $this->assertDatabaseMissing('user_cards', [
            'id' => $userCardId2,
        ]);
        $this->assertDatabaseMissing('user_deck_cards', [
            'user_deck_id' => $userDeck->id,
            'user_card_id' => $userCardId2,
        ]);
        $this->assertDatabaseMissing('user_decks', [
            'id' => $userDeck->id,
        ]);
    }
}
