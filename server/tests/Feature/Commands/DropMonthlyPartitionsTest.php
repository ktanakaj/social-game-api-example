<?php

namespace Tests\Feature\Console\Commands\Rankings;

use Tests\TestCase;

class DropMonthlyPartitionsTest extends TestCase
{
    /**
     * 月単位のDBパーティションのDROPコマンドのテスト。
     */
    public function test() : void
    {
        // ※ sqliteではパーティションは使用できないので、コマンドが呼べることだけ確認
        $this->assertSame(0, \Artisan::call('partition:drop', ['year' => 2019, 'month' => 3, 'classname' => \App\Models\Globals\UserGift::class]));
        $outputed = \Artisan::output();
        $this->assertRegExp('|^Drop monthlly partition table=user_gifts date=2019/3 : droping...$|m', $outputed);
        $this->assertRegExp('|^The partition is not found. skipped.$|m', $outputed);
    }
}
