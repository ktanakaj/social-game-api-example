<?php

namespace App\Models\Masters;

use App\Enums\ItemType;
use App\Models\CamelcaseJson;

/**
 * アイテムマスタを表すモデル。
 *
 * アイテムはユーザーが所持するもので、例えば以下のような種類がある想定。
 * ・スタミナ回復
 * ・経験値獲得
 * ・強化素材
 * ・ガチャ券
 * ・交換用メダル等
 */
class Item extends MasterModel
{
    use CamelcaseJson;

    /**
     * 日付として扱う属性。
     * @var array
     */
    protected $dates = [
        'expired_at',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'rarity' => 'integer',
        'effect' => 'array',
        'expired_at' => 'timestamp',
    ];

    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'effect' => '{}',
    ];

    /**
     * アイテム種別を保存する。
     * @param mixed $value 値。
     */
    public function setTypeAttribute($value) : void
    {
        // マスタインポート用。アイテム種別をバリデーションする
        if (!in_array($value, ItemType::values())) {
            throw new \InvalidArgumentException("type=\"{$value}\" is not found");
        }
        $this->attributes['type'] = $value;
    }

    /**
     * アイテム効果を保存する。
     * @param mixed $value 値。
     */
    public function setEffectAttribute($value) : void
    {
        // マスタインポート用。ミューテタが無いとJSON文字列が$castsで
        // 二重にエスケープされるので、空定義でそれを阻止する
        $this->attributes['effect'] = $value;
    }
}
