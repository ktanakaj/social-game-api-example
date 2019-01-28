<?php

namespace App\Exceptions;

/**
 * 権限無しの例外クラス。
 */
class ForbiddenException extends AppException
{
    /**
     * 例外を生成する。
     * @param string $message エラーメッセージ。
     */
    public function __construct(string $message) {
        parent::__construct($message, 'FORBIDDEN');
    }
}