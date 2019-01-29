<?php

namespace App\Exceptions;

/**
 * データ競合の例外クラス。
 */
class ConflictException extends AppException
{
    /**
     * 例外を生成する。
     * @param string $message エラーメッセージ。
     * @param mixed $data 追加のエラー情報。
     */
    public function __construct(string $message, $data = null)
    {
        parent::__construct($message, 'CONFLICT', $data);
    }
}
