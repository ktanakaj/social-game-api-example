<?php

namespace App\Models\Masters;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
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
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * 有効なガチャのみを取得するクエリスコープ。
     */
    public function scopeActive($query, \DateTimeInterface $date = null)
    {
        // 有効な価格があるものを有効なマスタと判定
        $date = $date ?? Carbon::now();
        return $query->whereHas('prices', function ($query) use ($date) {
            return $query->active($date);
        });
    }

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
        // ガチャ内でランダムに抽選。どれかが必ず当たる
        // ※ ユーザーに表示している確率と確実に一致させるという意味で、
        //    weightではなく↓のgetRates()を使って抽選した方がよいかも？
        //    ただ、そうすると計算がfloatになるので、端数で事故るかもしれない。
        $drops = $this->drops()->active()->get();
        $total = $drops->sum('weight');
        $rnd = random_int(1, $total);
        $sum = 0;
        foreach ($drops as $drop) {
            $sum += $drop->weight;
            if ($rnd <= $sum) {
                return new ObjectInfo($drop);
            }
        }
        throw new \LogicException("gacha lot failed (gachaId={$this->id})");
    }

    /**
     * ガチャの排出確率一覧を取得する。
     * @return Collection 排出確率を設定したGachaDropのコレクション。
     */
    public function getRates() : Collection
    {
        // GachaDropにrateを設定して返す
        $drops = $this->drops()->active()->get();
        $total = floatval($drops->sum('weight'));
        foreach ($drops as $drop) {
            // ※ モデルに、モデル外の列を追加するの微妙？とはいえ、totalが必要なのでモデルだけでは処理できない
            $drop->rate = $drop->weight / $total;
        }
        return $drops;
    }
}
