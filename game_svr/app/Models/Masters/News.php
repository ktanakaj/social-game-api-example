<?php

namespace App\Models\Masters;

/**
 * お知らせのマスタを表すモデル。
 */
class News extends MasterModel
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
