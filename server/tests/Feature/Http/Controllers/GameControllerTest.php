<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Enums\ObjectType;
use App\Enums\QuestStatus;
use App\Models\Globals\User;

class GameControllerTest extends TestCase
{
    /**
     * ゲーム開始/終了のテスト。
     */
    public function testStartAndEnd() : void
    {
        $user = factory(User::class)->create();
        $userAchievement1 = $user->achievements->where('achievement_id', 10000)->first();
        $userAchievement2 = $user->achievements->where('achievement_id', 20000)->first();

        // 普通のクエストの初回プレイ開始
        $response = $this->withLogin($user)->json('POST', '/game/start', [
            'questId' => 2,
            'deckId' => $user->last_selected_deck_id,
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('questlogId', $json);
        $this->assertSame($user->stamina - 5, $json['stamina']);

        $questlogId = $json['questlogId'];
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'stamina' => $user->stamina - 5,
        ]);
        $this->assertDatabaseHas('questlogs', [
            'id' => $questlogId,
            'user_id' => $user->id,
            'quest_id' => 2,
            'status' => QuestStatus::STARTED,
        ]);

        // プレイ終了。初回の固定の報酬が付与される
        $response = $this->withLogin($user)->json('POST', '/game/end', [
            'questlogId' => $questlogId,
            'status' => QuestStatus::SUCCEED,
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertCount(3, $json);

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

        $received = $json[2];
        $this->assertSame('card', $received['type']);
        $this->assertSame(1100, $received['id']);
        $this->assertSame(1, $received['count']);
        $this->assertFalse($received['isNew']);

        $this->assertDatabaseHas('user_quests', [
            'user_id' => $user->id,
            'quest_id' => 2,
            'count' => 1,
        ]);
        $this->assertDatabaseHas('questlogs', [
            'id' => $questlogId,
            'user_id' => $user->id,
            'quest_id' => 2,
            'status' => 'succeed',
        ]);
        // アチーブメントデータは、ランダム分によってばらつきがあるのでそれを加味して比較
        $score = $userAchievement1 ? $userAchievement1->score : 0;
        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_id' => 10000,
            'score' => $score >= 10 ? 10 : $score + 1,
        ]);
        $score = $userAchievement2 ? $userAchievement2->score : 0;
        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_id' => 20000,
            'score' => $score >= 100 ? 100 : $score + 1,
        ]);
    }

    /**
     * ゲーム開始/終了（2週目以降）のテスト。
     */
    public function testStartAndEndFor2ndTime() : void
    {
        // 報酬が2回目以降用のものになるのと、クリア回数がカウントアップされるのを確認
        $user = factory(User::class)->create();
        $userQuest = $user->quests[0];

        $response = $this->withLogin($user)->json('POST', '/game/start', [
            'questId' => 1,
            'deckId' => $user->last_selected_deck_id,
        ]);
        $response->assertStatus(200);
        $questlogId = $response->json()['questlogId'];
        $response = $this->withLogin($user)->json('POST', '/game/end', [
            'questlogId' => $questlogId,
            'status' => QuestStatus::SUCCEED,
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertCount(2, $json);

        $received = $json[0];
        $this->assertSame('exp', $received['type']);
        $this->assertNull($received['id']);
        $this->assertSame(20, $received['count']);
        $this->assertFalse($received['isNew']);

        $received = $json[1];
        $this->assertSame('gameCoin', $received['type']);
        $this->assertNull($received['id']);
        $this->assertLessThanOrEqual(60, $received['count']);
        $this->assertFalse($received['isNew']);

        $this->assertDatabaseHas('user_quests', [
            'user_id' => $user->id,
            'quest_id' => 1,
            'count' => $userQuest->count + 1,
        ]);
    }

    /**
     * ゲーム開始/終了（失敗）のテスト。
     */
    public function testStartAndEndAtFailed() : void
    {
        // 報酬が無く、クリア済みにもならないのを確認
        $user = factory(User::class)->create();

        $response = $this->withLogin($user)->json('POST', '/game/start', [
            'questId' => 2,
            'deckId' => $user->last_selected_deck_id,
        ]);
        $response->assertStatus(200);
        $questlogId = $response->json()['questlogId'];
        $response = $this->withLogin($user)->json('POST', '/game/end', [
            'questlogId' => $questlogId,
            'status' => QuestStatus::FAILED,
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertCount(0, $json);

        $this->assertDatabaseMissing('user_quests', [
            'user_id' => $user->id,
            'quest_id' => 2,
        ]);
    }

    /**
     * ゲーム開始（スタミナ不足）のテスト。
     */
    public function testStartWhenEmptyStamina() : void
    {
        $user = factory(User::class)->create();
        $user->stamina = 4;
        $user->save();

        $response = $this->withLogin($user)->json('POST', '/game/start', [
            'questId' => 2,
            'deckId' => $user->last_selected_deck_id,
        ]);
        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => [
                    'code' => 'EMPTY_RESOURCE',
                    'message' => 'stamina = -1 is invalid',
                    'data' => [
                        'type' => ObjectType::STAMINA,
                        'count' => 1,
                    ],
                ],
            ]);
    }

    /**
     * ゲーム開始（前クエスト未達成）のテスト。
     */
    public function testStartAtPreviousIdIsNotFound() : void
    {
        $user = factory(User::class)->create();

        $response = $this->withLogin($user)->json('POST', '/game/start', [
            'questId' => 3,
            'deckId' => $user->last_selected_deck_id,
        ]);
        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => [
                    'code' => 'BAD_REQUEST',
                    'message' => 'The given data was invalid. (validation.exists:user_quests,quest_id)',
                ],
            ]);
    }

    /**
     * ゲーム開始/終了（プレイ済）のテスト。
     */
    public function testStartAndEndAtNotStarted() : void
    {
        // 一度プレイ済みのクエストログIDを再度呼ぶとデータ無しで失敗
        $user = factory(User::class)->create();

        $response = $this->withLogin($user)->json('POST', '/game/start', [
            'questId' => 2,
            'deckId' => $user->last_selected_deck_id,
        ]);
        $response->assertStatus(200);

        $body = [
            'questlogId' => $response->json()['questlogId'],
            'status' => QuestStatus::SUCCEED,
        ];
        $response = $this->withLogin($user)->json('POST', '/game/end', $body);
        $response->assertStatus(200);

        $response = $this->withLogin($user)->json('POST', '/game/end', $body);
        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => [
                    'code' => 'BAD_REQUEST',
                    'message' => 'The given data was invalid. (The selected questlog id is invalid.)',
                ],
            ]);
    }
}
