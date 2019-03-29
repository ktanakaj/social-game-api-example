<?php

namespace Tests\Unit\Models\Masters;

use Tests\TestCase;
use App\Models\Masters\Drop;

class DropTest extends TestCase
{
    /**
     * ドロップセットから報酬を抽選のテスト。
     */
    public function testLot() : void
    {
        // 何度も回して、rate通りの確率で出ることを確認する。
        // また個数はrangeの範囲でばらつくことも確認する。
        $count = 300;
        $rates = [];
        $totals = [];
        for ($i = 0; $i < $count; $i++) {
            foreach (Drop::lot(202) as $info) {
                $key = "{$info->type}:{$info->id}";
                if (!isset($rates[$key])) {
                    $rates[$key] = 0;
                    $totals[$key] = 0;
                }
                ++$rates[$key];
                $totals[$key] += $info->count;
            }
        }

        // 経験値とコインが100%で、カードは10%なので、それに近い確率ならOK
        $this->assertSame($count, $rates['exp:']);
        $this->assertSame($count, $rates['gameCoin:']);
        $this->assertEqualsWithDelta(0.10, floatval($rates['card:1100']) / $count, 0.05);

        // 個数はコインだけバラつきがある
        $this->assertSame($count * 20, $totals['exp:']);
        $this->assertEqualsWithDelta($count * 50, $totals['gameCoin:'], $count * 5);
        $this->assertSame($rates['card:1100'], $totals['card:1100']);
    }
}
