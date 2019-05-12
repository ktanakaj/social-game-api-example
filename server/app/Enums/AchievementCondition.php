<?php

namespace App\Enums;

/**
 * アチーブメントの条件を表すEnum定義。
 */
final class AchievementCondition
{
    // ※ Enumと言いつつ、現状ただのconst定義。必要ならphp-enum等も検討
    /** アチーブメント条件: ユーザーレベル */
    const LEVEL = 'level';
    /** アチーブメント条件: いずれかのクエスト */
    const ANY_QUESTS = 'anyQuests';

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
            self::LEVEL,
            self::ANY_QUESTS,
        ];
    }
}
