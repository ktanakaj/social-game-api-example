<?php

namespace App\Models\Masters;

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
    /** アイテム種別: 消費アイテム */
    const ITEM_TYPE_USABLE = 'usable';
    /** アイテム種別: 素材アイテム */
    const ITEM_TYPE_MATERIAL = 'material';
    /** アイテム種別: 交換用アイテム */
    const ITEM_TYPE_TRADABLE = 'tradable';
    /** アイテム種別: ガチャチケット等 */
    const ITEM_TYPE_TICKET = 'ticket';

    /** アイテム種別一覧 */
    const ITEM_TYPES = [
        self::ITEM_TYPE_USABLE,
        self::ITEM_TYPE_MATERIAL,
        self::ITEM_TYPE_TRADABLE,
        self::ITEM_TYPE_TICKET,
    ];

    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'effect' => '{}',
    ];

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
     * アイテム効果を保存する。
     * @param mixed $value 値。
     */
    public function setEffectAttribute($value) : void
    {
        // マスタインポート用。ミューテタが無いとJSON文字列が$castsで
        // 二重にエスケープされるので、空定義でそれを阻止する
        $this->attributes['effect'] = $value;
    }

    /**
     * アイテム期限を保存する。
     * @param mixed $value 値。
     */
    public function setExpiredAtAttribute($value) : void
    {
        // マスタインポート用。CSVから空文字列が渡されるとエラーになるので、
        // nullに読み替え
        $this->attributes['expired_at'] = $value !== '' ? $value : null;
    }
}
