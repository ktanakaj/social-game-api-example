<?php

namespace Tests\Unit\Models\Globals;

use Tests\TestCase;
use App\Models\Globals\UserCard;
use App\Models\Virtual\ObjectInfo;

class UserCardTest extends TestCase
{
    /**
     * カードを受け取るのテスト。
     */
    public function testReceiveTo() : void
    {
        // テストデータを作って、そこに受け取り
        // ※ プレイヤーはカードを持っていない想定
        $user = $this->createTestUser();

        // 1枚目の受け取り
        $received = UserCard::receiveTo($user->id, new ObjectInfo(['id' => 2000]));

        $this->assertSame(2000, $received->id);
        $this->assertSame(1, $received->count);
        $this->assertNull($received->total);
        $this->assertTrue($received->is_new);

        $this->assertDatabaseHas('user_cards', [
            'user_id' => $user->id,
            'card_id' => 2000,
            'count' => 1,
        ]);

        $this->assertSame(1, $user->cards()->count());

        // 2枚目3枚目の受け取り。特に合算されず新しいカードが付与される
        $received = UserCard::receiveTo($user->id, new ObjectInfo(['id' => 2000, 'count' => 2]));

        $this->assertSame(2000, $received->id);
        $this->assertSame(2, $received->count);
        $this->assertNull($received->total);
        $this->assertFalse($received->is_new);

        $this->assertSame(3, $user->cards()->count());
    }
}
