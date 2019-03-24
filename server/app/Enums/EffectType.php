<?php

namespace App\Enums;

/**
 * エフェクト（アイテムの効果等）の種別を表すEnum定義。
 */
final class EffectType
{
    // ※ Enumと言いつつ、現状ただのconst定義。必要ならphp-enum等も検討
    /** エフェクト種別: 経験値増減 */
    const EXP = 'exp';
    /** エフェクト種別: スタミナ増減 */
    const STAMINA = 'stamina';

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
            self::EXP,
            self::STAMINA,
        ];
    }
}
