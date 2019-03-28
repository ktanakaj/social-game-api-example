<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ObjectType;
use App\Models\CamelcaseJson;
use App\Models\HasPeriod;

/**
 * ガチャ排出品マスタを表すモデル。
 *
 * ガチャで排出される品（カード、アイテム等）のラインナップと確率を定義する。
 */
class GachaDrop extends MasterModel
{
    use CamelcaseJson, HasPeriod;

    /**
     * 主キーがインクリメントされるかの指示。
     * @var bool
     */
    public $incrementing = true;

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
        'gacha_id' => 'integer',
        'object_id' => 'integer',
        'count' => 'integer',
        'weight' => 'integer',
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
