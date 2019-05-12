<?php

namespace Tests\Unit\Models\Masters;

use Tests\TestCase;
use App\Models\Masters\Gacha;

class GachaTest extends TestCase
{
    /**
     * ガチャの排出物を抽選のテスト。
     */
    public function testLot() : void
    {
        // 単純なガチャでの検証。何度も回して、weight通りの確率で出ることを確認する。
        // ※ 仕組み上、稀にテスト失敗になる可能性があり。
        $gacha = Gacha::findOrFail(1);
        $count = 300;
        $rates = [];
        for ($i = 0; $i < $count; $i++) {
            $info = $gacha->lot();
            $key = "{$info->type}:{$info->id}:{$info->count}";
            if (!isset($rates[$key])) {
                $rates[$key] = 0;
            }
            ++$rates[$key];
        }

        // レアガチャは3枚が均等なので、33%前後で出てればOK
        $this->assertEqualsWithDelta(0.333, floatval($rates['card:1000:1']) / $count, 0.06);
        $this->assertEqualsWithDelta(0.333, floatval($rates['card:1100:1']) / $count, 0.06);
        $this->assertEqualsWithDelta(0.333, floatval($rates['card:1200:1']) / $count, 0.06);
    }

    /**
     * ガチャの排出物を抽選のテスト2。
     */
    public function testLot2() : void
    {
        // 複雑なガチャでの検証。何度も回して、weight通りの確率で出ることを確認する。
        // ※ 仕組み上、稀にテスト失敗になる可能性があり。
        $gacha = Gacha::findOrFail(2);
        $count = 500;
        $rates = [];
        for ($i = 0; $i < $count; $i++) {
            $info = $gacha->lot();
            $key = "{$info->type}:{$info->id}:{$info->count}";
            if (!isset($rates[$key])) {
                $rates[$key] = 0;
            }
            ++$rates[$key];
        }

        $this->assertEqualsWithDelta(0.238, floatval($rates['card:2000:1']) / $count, 0.06);
        $this->assertEqualsWithDelta(0.238, floatval($rates['card:2100:1']) / $count, 0.06);
        $this->assertEqualsWithDelta(0.238, floatval($rates['card:2200:1']) / $count, 0.06);
        $this->assertEqualsWithDelta(0.023, floatval($rates['card:1100:1']) / $count, 0.06);
        $this->assertEqualsWithDelta(0.023, floatval($rates['card:1200:1']) / $count, 0.06);
        $this->assertEqualsWithDelta(0.238, floatval($rates['item:200:1']) / $count, 0.06);
    }
}
