<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CamelcaseJson;
use App\Models\Virtual\ObjectInfo;

/**
 * ガチャマスタを表すモデル。
 *
 * ガチャマスタではガチャのIDや名称といった大本の定義を行う。
 * 価格や排出物は、サブテーブルで定義する。
 * （ガチャの有効期間も、価格が全て期間外の場合、といった形で行う。）
 */
class Gacha extends MasterModel
{
    use CamelcaseJson;

    /**
     * ガチャ価格とのリレーション定義。
     */
    public function prices() : HasMany
    {
        return $this->hasMany('App\Models\Masters\GachaPrice');
    }

    /**
     * ガチャ排出物とのリレーション定義。
     */
    public function drops() : HasMany
    {
        return $this->hasMany('App\Models\Masters\GachaDrop');
    }

    /**
     * ガチャの排出物を抽選する。
     * @return ObjectInfo 抽選されたObjectInfo。
     */
    public function lot() : ObjectInfo
    {
        // ガチャ内でランダムに抽選。どれかが必ず当たる。
        $drops = $this->drops()->active()->get();
        $total = $drops->sum('weight');
        $rnd = random_int(0, $total);
        $sum = 0;
        foreach ($drops as $drop) {
            $sum += $drop->weight;
            if ($rnd <= $sum) {
                return new ObjectInfo($drop);
            }
        }
        throw new \LogicException("gacha lot failed (gachaId={$this->id})");
    }
}
