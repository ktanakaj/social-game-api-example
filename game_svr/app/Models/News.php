<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * お知らせのマスタを表すモデル。
 */
class News extends Model
{
    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'title' => '{}',
        'body' => '{}',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'title' => 'array',
        'body' => 'array',
    ];
}
