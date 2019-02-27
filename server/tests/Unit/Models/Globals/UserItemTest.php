<?php

namespace Tests\Unit\Models\Globals;

use Tests\TestCase;
use App\Models\Globals\UserItem;
use App\Models\Virtual\ObjectInfo;

class UserItemTest extends TestCase
{
    /**
     * アイテムを受け取るのテスト。
     */
    public function testReceiveTo() : void
    {
        // テストデータを作って、そこに受け取り
        // ※ プレイヤーはアイテムを持っていない想定
        $user = $this->createTestUser();

        // 1個受け取り
        $received = UserItem::receiveTo($user->id, new ObjectInfo(['id' => 110]));

        $this->assertSame(110, $received->id);
        $this->assertSame(1, $received->count);
        $this->assertSame(1, $received->total);
        $this->assertTrue($received->is_new);

        $this->assertDatabaseHas('user_items', [
            'user_id' => $user->id,
            'item_id' => 110,
            'count' => 1,
        ]);

        // 2個目3個目の受け取り。既存のものに合算される
        $received = UserItem::receiveTo($user->id, new ObjectInfo(['id' => 110, 'count' => 2]));

        $this->assertSame(110, $received->id);
        $this->assertSame(2, $received->count);
        $this->assertSame(3, $received->total);
        $this->assertFalse($received->is_new);

        $this->assertDatabaseHas('user_items', [
            'user_id' => $user->id,
            'item_id' => 110,
            'count' => 3,
        ]);
    }
}
