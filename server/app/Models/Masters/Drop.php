<?php

namespace App\Models\Masters;

use App\Enums\ObjectType;
use App\Models\CamelcaseJson;
use App\Models\Virtual\ObjectInfo;

/**
 * ドロップ報酬マスタを表すモデル。
 *
 * クエストなどで落ちる報酬の内容や確率を扱う。
 * ドロップセットID単位で報酬セットを構成しており、
 * セット内でグループごとにどれかの報酬が落ちる。
 */
class Drop extends MasterModel
{
    use CamelcaseJson;

    /**
     * 主キーがインクリメントされるかの指示。
     * @var bool
     */
    public $incrementing = true;

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'drop_set_id' => 'integer',
        'group' => 'integer',
        'rate' => 'integer',
        'object_id' => 'integer',
        'count' => 'integer',
        'range' => 'integer',
    ];

    /**
     * オブジェクト種別を保存する。
     * @param mixed $value 値。
     */
    public function setObjectTypeAttribute($value) : void
    {
        // マスタインポート用。オブジェクト種別をバリデーションする
        if (!in_array($value, ObjectType::values())) {
            throw new \InvalidArgumentException("object_type=\"{$value}\" is not found");
        }
        $this->attributes['object_type'] = $value;
    }

    /**
     * ドロップセットから報酬を抽選する。
     * @param int $dropSetId ドロップセットID。
     * @return array 報酬のObjectInfo配列。
     */
    public static function lot(int $dropSetId) : array
    {
        // 同一ドロップセットIDのアイテムを、同一グループのものごとに処理
        $dropsByGroups = static::where('drop_set_id', $dropSetId)->orderBy('group', 'asc')->get()->groupBy('group');
        $infoArray = [];
        foreach ($dropsByGroups as $drops) {
            // 同一グループ内でランダムに確率を元に抽選。どれかが当たれば辺り
            // ※ 積み上げ式なので、グループに50%のものが二つあればどちらかは当たる
            $rnd = random_int(0, 100);
            $rate = 0;
            $dropped = null;
            foreach ($drops->shuffle() as $drop) {
                $rate += $drop->rate;
                if ($rnd <= $rate) {
                    $dropped = $drop;
                    break;
                }
            }
            if ($dropped) {
                // レンジが指定されている場合は、結果の個数に幅を持たせる
                $info = new ObjectInfo($dropped);
                if ($dropped->range) {
                    $info->count += round($info->count * random_int(-$dropped->range, $dropped->range) / 100.0);
                }
                if ($info->count > 0) {
                    $infoArray[] = $info;
                }
            }
        }
        return $infoArray;
    }
}
