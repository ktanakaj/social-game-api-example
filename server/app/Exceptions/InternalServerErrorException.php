<?php

namespace App\Exceptions;

/**
 * サーバーエラーの例外クラス。
 */
class InternalServerErrorException extends AppException
{
    /**
     * 例外を生成する。
     * @param string $message エラーメッセージ。
     * @param \Throwable $previous 元となった例外。
     */
    public function __construct(string $message, \Throwable $previous = null) {
        parent::__construct($message, 'INTERNAL_SERVER_ERROR', $previous);
    }
}