<?php

namespace App\Enums;

/**
 * 管理者のロールを表すEnum定義。
 */
final class AdminRole
{
    // ※ Enumと言いつつ、現状ただのconst定義。必要ならphp-enum等も検討
    /** 管理者ロール: 管理者 */
    const ADMIN = 0;
    /** 管理者ロール: 書き込み可 */
    const WRITABLE = 1;
    /** 管理者ロール: 読み取り専用 */
    const READONLY = 2;

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
            self::ADMIN,
            self::WRITABLE,
            self::READONLY,
        ];
    }
}
