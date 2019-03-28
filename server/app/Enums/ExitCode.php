<?php

namespace App\Enums;

/**
 * コンソールコマンド戻り値のEnum定義。
 */
final class ExitCode
{
    // https://www.freebsd.org/cgi/man.cgi?query=sysexits&apropos=0&sektion=0&manpath=FreeBSD+4.3-RELEASE&format=html
    // ↑この辺もとに定義

    /** 戻り値: 成功 */
    const SUCCESS = 0;
    /** 戻り値: 失敗 */
    const FAILURE = 1;
    /** 戻り値: 引き数誤り */
    const USAGE = 64;
    /** 戻り値: 入力データ誤り */
    const DATAERR = 65;
    /** 戻り値: 入力データ無し */
    const NOINPUT = 66;
    /** 戻り値: 指定されたユーザーが無い */
    const NOUSER = 67;
    /** 戻り値: 指定されたホストが無い */
    const NOHOST = 68;
    /** 戻り値: 利用不可 */
    const UNAVAILABLE = 69;
    /** 戻り値: ソフトウェアの内部的エラー */
    const SOFTWARE = 70;
    /** 戻り値: OSのエラー */
    const OSERR = 71;
    /** 戻り値: システムファイルが無い等 */
    const OSFILE = 72;
    /** 戻り値: ファイルが出力できない */
    const CANTCREAT = 73;
    /** 戻り値: I/Oエラー */
    const IOERR = 74;
    /** 戻り値: 一時的エラー */
    const TEMPFAIL = 75;
    /** 戻り値: リモートシステムのプロトコルエラー */
    const PROTOCOL = 76;
    /** 戻り値: アプリ内の権限エラー */
    const NOPERM = 77;
    /** 戻り値: 設定ミスなど */
    const CONFIG = 78;

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
            self::SUCCESS,
            self::FAILURE,
            self::USAGE,
            self::DATAERR,
            self::NOINPUT,
            self::NOUSER,
            self::NOHOST,
            self::UNAVAILABLE,
            self::SOFTWARE,
            self::OSERR,
            self::OSFILE,
            self::CANTCREAT,
            self::IOERR,
            self::TEMPFAIL,
            self::PROTOCOL,
            self::NOPERM,
            self::CONFIG,
        ];
    }
}
