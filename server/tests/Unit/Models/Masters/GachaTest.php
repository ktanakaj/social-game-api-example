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
        // 何度も回して、weight通りの確率で出ることを確認する
        $gacha = Gacha::findOrFail(1);
        $count = 10000;
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
        $this->assertEquals(0.333, floatval($rates['card:1000:1']) / $count, 0.03);
        $this->assertEquals(0.333, floatval($rates['card:1100:1']) / $count, 0.03);
        $this->assertEquals(0.333, floatval($rates['card:1200:1']) / $count, 0.03);
    }
}
