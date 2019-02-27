<?php

namespace App\Enums;

/**
 * アイテムの種別を表すEnum定義。
 */
final class ItemType
{
    // ※ Enumと言いつつ、現状ただのconst定義。必要ならphp-enum等も検討
    /** アイテム種別: 消費アイテム */
    const USABLE = 'usable';
    /** アイテム種別: 素材アイテム */
    const MATERIAL = 'material';
    /** アイテム種別: 交換用アイテム */
    const TRADABLE = 'tradable';
    /** アイテム種別: ガチャチケット等 */
    const TICKET = 'ticket';

    /** new抑止用コンストラクタ。 */
    private function __construct()
    {
    }

    /**
     * 全定数値を取得する。
     * @return array 定数値配列。
     */
    public static function values() : array
    {
        return [
            self::USABLE,
            self::MATERIAL,
            self::TRADABLE,
            self::TICKET,
        ];
    }
}
