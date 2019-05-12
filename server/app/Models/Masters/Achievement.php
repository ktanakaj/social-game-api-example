<?php

namespace App\Models\Masters;

use App\Enums\ObjectType;
use App\Models\CamelcaseJson;
use App\Models\HasPeriod;

/**
 * アチーブメントマスタを表すモデル。
 *
 * アチーブメントは、ユーザーが何かを達成したタイミングで報酬を付与するもの。
 * （ゲームによってはミッションやトロフィーとも呼ばれるもの。）
 * 一度だけ挑戦できる「通常」と、1日ごとにリセットされる「デイリー」、
 * 1週間ごとにリセットされる「ウィークリー」がある。
 */
class Achievement extends MasterModel
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
        'score' => 'integer',
        'options' => 'array',
        'object_id' => 'integer',
        'count' => 'integer',
        'open_at' => 'timestamp',
        'close_at' => 'timestamp',
    ];

    /**
     * アチーブメント追加条件を保存する。
     * @param mixed $value 値。
     */
    public function setOptionsAttribute($value) : void
    {
        // マスタインポート用。ミューテタが無いとJSON文字列が$castsで
        // 二重にエスケープされるので、空定義でそれを阻止する
        $this->attributes['options'] = $value;
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
