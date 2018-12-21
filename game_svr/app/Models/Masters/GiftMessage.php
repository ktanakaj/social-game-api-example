<?php

namespace App\Models\Masters;

/**
 * プレゼントのメッセージのマスタを表すモデル。
 */
class GiftMessage extends MasterModel
{
    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'message' => '{}',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'message' => 'array',
    ];
}
