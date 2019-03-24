<?php

namespace App\Models;

/**
 * エフェクト適用クラス。
 *
 * 消費系アイテムなどの効果（エフェクト）適用の窓口として機能する。
 * 処理の実態は各クラスに定義して、その関数をエフェクターに登録する。
 */
final class Effector
{
    /** new抑止用コンストラクタ。 */
    private function __construct()
    {
    }

    /**
     * エフェクトを適用する処理。
     * @var array
     */
    protected static $effectors = [];

    /**
     * エフェクト適用処理を登録する。
     *
     * $effector は int $userId, string $value, string $type を引数に取り、
     * ReceivedInfoインスタンスまたはnullを返す。
     * 効果が適用できない（適用条件があるなど）場合は無視したり例外を投げたりする。
     *
     * ※ 現状、消費系アイテムをベースに設計。今後種類が増える場合、パラメータの他、名称変更やクラス分割も含めて見直し発生するかも。
     * @param string $type エフェクト種別。
     * @param callable $effector エフェクト適用処理。
     */
    public static function effector(string $type, callable $effector) : void
    {
        static::$effectors[$type] = $effector;
    }

    /**
     * エフェクトを適用する。
     * @param int $userId 効果を適用するユーザーのID。
     * @param array $effects エフェクト種別をキー、設定値を値、とする連想配列。
     * @return array 適用結果のReceivedInfo配列。
     */
    public static function effect(int $userId, array $effects) : array
    {
        $results = [];
        foreach ($effects as $type => $value) {
            if (!isset(static::$effectors[$type])) {
                throw new \LogicException("effectType={$type} is not supported");
            }
            if ($result = call_user_func(static::$effectors[$type], $userId, $value, $type)) {
                $results[] = $result;
            }
        }
        return $results;
    }

    /**
     * 値を加減算するようなエフェクト効果の計算を行う。
     * @param float $target エフェクトを適用する値。
     * @param string $effectValue エフェクトの設定値。
     * @return float 計算した値。
     */
    public static function calcEffect(float $target, string $effectValue) : float
    {
        // +100, -50, *1.5 のような算術記号付の場合は元の値に計算する。
        // 記号がない場合は、絶対値が指定されているものとして単純に上書きする。
        $mark = substr($effectValue, 0, 1);
        $rate = substr($effectValue, 1);
        if ($mark === false || $rate === false) {
            return $effectValue;
        }
        $rate = trim($rate);
        // TODO: スタミナ50%回復などを想定して*も作ったが、*の場合呼び元も引数を変えないと駄目なのでこれじゃ使い辛そう。考える。
        //       （引数に最大値も渡す？+100%のような指定も可能にする？）
        switch ($mark) {
            case '+':
                return $target + $rate;
            case '-':
                return $target - $rate;
            case '*':
                return $target * $rate;
            case '/':
                return $target / $rate;
            default:
                throw new \LogicException("effectValue={$effectValue} is not supported");
        }
    }
}
