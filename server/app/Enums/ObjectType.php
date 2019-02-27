<?php

namespace App\Enums;

/**
 * オブジェクト（アイテムやカード等）の種別を表すEnum定義。
 */
final class ObjectType
{
    // ※ Enumと言いつつ、現状ただのconst定義。必要ならphp-enum等も検討
    /** オブジェクト種別: ゲームコイン */
    const GAME_COIN = 'gameCoin';
    /** オブジェクト種別: 課金コイン */
    const SPECIAL_COIN = 'specialCoin';
    /** オブジェクト種別: 経験値 */
    const EXP = 'exp';
    /** オブジェクト種別: カード */
    const CARD = 'card';
    /** オブジェクト種別: アイテム */
    const ITEM = 'item';

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
            self::GAME_COIN,
            self::SPECIAL_COIN,
            self::EXP,
            self::CARD,
            self::ITEM,
        ];
    }
}
