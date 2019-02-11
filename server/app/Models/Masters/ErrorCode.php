<?php

namespace App\Models\Masters;

/**
 * エラーコードマスタモデル。
 */
class ErrorCode extends MasterModel
{
    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'response_code' => 'integer',
    ];
}
