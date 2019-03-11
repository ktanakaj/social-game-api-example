<?php

namespace App\Enums;

/**
 * クエスト実行状態を表すEnum定義。
 */
final class QuestStatus
{
    // ※ Enumと言いつつ、現状ただのconst定義。必要ならphp-enum等も検討
    /** クエスト実行状態: 開始 */
    const STARTED = 'started';
    /** クエスト実行状態: 成功 */
    const SUCCEED = 'succeed';
    /** クエスト実行状態: 失敗 */
    const FAILED = 'failed';

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
            self::STARTED,
            self::SUCCEED,
            self::FAILED,
        ];
    }
}
