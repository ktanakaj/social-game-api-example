<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ObjectType;
use App\Models\CamelcaseJson;
use App\Models\HasPeriod;

/**
 * ガチャ価格マスタを表すモデル。
 *
 * ガチャの価格とx連回数、販売期間などを定義する。
 */
class GachaPrice extends MasterModel
{
    use CamelcaseJson, HasPeriod;

    /**
     * 日付として扱う属性。
     * @var array
     */
    protected $dates = [
        'open_at',
        'close_at',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'gacha_id' => 'integer',
        'object_id' => 'integer',
        'prices' => 'integer',
        'times' => 'integer',
        'open_at' => 'timestamp',
        'close_at' => 'timestamp',
    ];

    /**
     * ガチャマスタとのリレーション定義。
     */
    public function gacha() : BelongsTo
    {
        return $this->belongsTo('App\Models\Masters\Gacha');
    }

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
}
