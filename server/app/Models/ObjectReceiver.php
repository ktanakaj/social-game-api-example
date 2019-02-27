<?php

namespace App\Models;

use App\Models\Virtual\ObjectInfo;
use App\Models\Virtual\ReceivedInfo;

/**
 * オブジェクト受け取り用のクラス。
 *
 * アイテムやカード、コインなどの受け取り処理の窓口として機能する。
 * 処理の実態は各クラスに定義して、その関数をレシーバーに登録する。
 */
final class ObjectReceiver
{
    /** new抑止用コンストラクタ。 */
    private function __construct()
    {
    }

    /**
     * オブジェクトを受け取る処理。
     * @var array
     */
    protected static $receiveres = [];

    /**
     * オブジェクト受け取り処理を登録する。
     *
     * $receiver は int $userId, ObjectInfo $info を引数に取り、
     * ReceivedInfoインスタンスを返す。
     * アイテムが持てないなど受け取れない場合は例外を投げる。
     * @param string $type オブジェクト種別。
     * @param callable $receiver オブジェクト受け取り処理。
     */
    public static function receiver(string $type, callable $receiver) : void
    {
        static::$receiveres[$type] = $receiver;
    }

    /**
     * オブジェクトを受け取る。
     * @param int $userId 受け取るユーザーのID。
     * @param object|array $info 受け取るオブジェクト {type, id, count} or {object_type, object_id, count}。
     * @return ReceivedInfo 受け取り結果。
     */
    public static function receive(int $userId, $info) : ReceivedInfo
    {
        if (!($info instanceof ObjectInfo)) {
            $info = new ObjectInfo($info);
        }
        if (!isset(static::$receiveres[$info->type])) {
            throw new \LogicException("objectType={$info->type} is not supported");
        }
        return call_user_func(static::$receiveres[$info->type], $userId, $info);
    }
}
