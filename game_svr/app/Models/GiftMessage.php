<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * プレゼントのメッセージのマスタを表すモデル。
 */
class GiftMessage extends Model
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
