<?php

namespace App\Exceptions;

/**
 * 不正なリクエストの例外クラス。
 */
class BadRequestException extends AppException
{
    /**
     * 例外を生成する。
     * @param string $message エラーメッセージ。
     * @param \Throwable $previous 元となった例外。
     */
    public function __construct(string $message, \Throwable $previous = null)
    {
        parent::__construct($message, 'BAD_REQUEST', null, $previous);
    }
}
