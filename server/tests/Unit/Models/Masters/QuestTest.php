<?php

namespace Tests\Unit\Models\Masters;

use Tests\TestCase;
use App\Models\Masters\Quest;

class QuestTest extends TestCase
{
    /**
     * 有効なクエストのみを取得するクエリスコープのテスト。
     */
    public function testScopeActive() : void
    {
        // id=3 が 2019/3/1 00:00 JST~ なのでそれでテスト
        $this->setTestNow('2019-02-28 23:59:59 JST');
        $this->assertNull(Quest::active()->find(3));

        $this->setTestNow('2019-03-01 00:00:00 JST');
        $this->assertNotNull(Quest::active()->find(3));
    }
}
