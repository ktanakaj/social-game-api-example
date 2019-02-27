<?php

namespace Tests\Unit\Models\Globals;

use Tests\TestCase;
use App\Models\Globals\User;
use App\Models\Virtual\ObjectInfo;

class UserTest extends TestCase
{
    /**
     * ゲームコインを受け取るのテスト。
     */
    public function testReceiveGameCoinTo() : void
    {
        // テストデータを作って、そこに受け取り
        $user = $this->createTestUser();

        $received = User::receiveGameCoinTo($user->id, new ObjectInfo(['count' => 1000]));

        $this->assertNull($received->id);
        $this->assertSame(1000, $received->count);
        $this->assertSame($user->game_coins + 1000, $received->total);
        $this->assertFalse($received->is_new);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'game_coins' => $user->game_coins + 1000,
        ]);
    }

    /**
     * スペシャルコインを受け取るのテスト。
     */
    public function testReceiveSpecialCoinTo() : void
    {
        // テストデータを作って、そこに受け取り
        $user = $this->createTestUser();

        $received = User::receiveSpecialCoinTo($user->id, new ObjectInfo(['count' => 1000]));

        $this->assertNull($received->id);
        $this->assertSame(1000, $received->count);
        $this->assertSame($user->special_coins + $user->free_special_coins + 1000, $received->total);
        $this->assertFalse($received->is_new);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'free_special_coins' => $user->free_special_coins + 1000,
            'special_coins' => $user->special_coins,
        ]);
    }

    /**
     * ユーザー経験値を受け取るのテスト。
     */
    public function testReceiveExpTo() : void
    {
        // テストデータを作って、そこに受け取り
        $user = $this->createTestUser();

        $received = User::receiveExpTo($user->id, new ObjectInfo(['count' => 1000]));

        $this->assertNull($received->id);
        $this->assertSame(1000, $received->count);
        $this->assertSame($user->exp + 1000, $received->total);
        $this->assertFalse($received->is_new);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'exp' => $user->exp + 1000,
        ]);
    }
}
