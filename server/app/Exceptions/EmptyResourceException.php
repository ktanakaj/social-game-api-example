<?php

namespace App\Exceptions;

use App\Models\Virtual\ObjectInfo;

/**
 * リソース不足の例外クラス。
 */
class EmptyResourceException extends AppException
{
    /**
     * 例外を生成する。
     * @param string $message エラーメッセージ。
     * @param ObjectInfo $info 足りなかったリソース。
     */
    public function __construct(string $message, ObjectInfo $resource = null)
    {
        parent::__construct($message, 'EMPTY_RESOURCE', $resource);
    }
}
