<?php

namespace App\Exceptions;

/**
 * 未認証の例外クラス。
 */
class UnauthorizedException extends AppException
{
    /**
     * 例外を生成する。
     * @param string $message エラーメッセージ。
     */
    public function __construct(string $message) {
        parent::__construct($message, 'UNAUTHORIZED');
    }
}