<?php

namespace App\Models\Masters;

use App\Models\CamelcaseJson;

/**
 * エラーコードマスタモデル。
 */
class ErrorCode extends MasterModel
{
    use CamelcaseJson;

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'response_code' => 'integer',
    ];
}
