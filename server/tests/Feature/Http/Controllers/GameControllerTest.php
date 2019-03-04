<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Globals\UserDeck;

class GameControllerTest extends TestCase
{
    /**
     * ゲーム開始/終了のテスト。
     */
    public function testStartAndEnd() : void
    {
        // テストデータを作ってクエスト実行
        $user = $this->createTestUser();
        $userCard = $user->cards()->create([
            'card_id' => 1000,
        ]);
        $userDeck = new UserDeck();
        $userDeck->no = 1;
        $user->decks()->save($userDeck);
        $userDeck->cards()->create(['userCardId' => $userCard->id, 'position' => 1]);

        // 普通のクエストの初回プレイ開始
        $response = $this->withLogin($user)->json('POST', '/game/start', [
            'questId' => 1,
            'deckId' => $userDeck->id,
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('questlogId', $json);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'stamina' => $user->stamina - 5,
        ]);
        $this->assertDatabaseHas('questlogs', [
            'id' => $json['questlogId'],
            'user_id' => $user->id,
            'quest_id' => 1,
            'status' => 'started',
        ]);

        // プレイ終了。初回の固定の報酬が付与される
        $questlogId = $json['questlogId'];
        $response = $this->withLogin($user)->json('POST', '/game/end', [
            'questlogId' => $json['questlogId'],
            'status' => 'succeed',
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertCount(2, $json);

        $received = $json[0];
        $this->assertSame('exp', $received['type']);
        $this->assertNull($received['id']);
        $this->assertSame(50, $received['count']);
        $this->assertFalse($received['isNew']);

        $received = $json[1];
        $this->assertSame('gameCoin', $received['type']);
        $this->assertNull($received['id']);
        $this->assertSame(100, $received['count']);
        $this->assertFalse($received['isNew']);

        $this->assertDatabaseHas('user_quests', [
            'user_id' => $user->id,
            'quest_id' => 1,
            'count' => 1,
        ]);
        $this->assertDatabaseHas('questlogs', [
            'id' => $questlogId,
            'user_id' => $user->id,
            'quest_id' => 1,
            'status' => 'succeed',
        ]);
    }

    // TODO: 2回目以降とか異常系とかもいろいろテスト書く
}
