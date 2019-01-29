<?php

namespace App\Exceptions;

/**
 * データ未存在の例外クラス。
 */
class NotFoundException extends AppException
{
    /**
     * 例外を生成する。
     * @param string $message エラーメッセージ。
     * @param mixed $data 追加のエラー情報。
     */
    public function __construct(string $message, $data = null)
    {
        parent::__construct($message, 'NOT_FOUND', $data);
    }
}
