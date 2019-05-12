<?php

namespace App\Enums;

/**
 * アチーブメントの種別を表すEnum定義。
 */
final class AchievementType
{
    // ※ Enumと言いつつ、現状ただのconst定義。必要ならphp-enum等も検討
    /** アチーブメント種別: 通常 */
    const NORMAL = 'normal';
    /** アチーブメント種別: デイリー */
    const DAILY = 'daily';
    /** アチーブメント種別: ウィークリー */
    const WEEKLY = 'weekly';

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
            self::NORMAL,
            self::DAILY,
            self::WEEKLY,
        ];
    }
}
