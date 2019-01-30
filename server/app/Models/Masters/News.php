<?php

namespace App\Models\Masters;

/**
 * お知らせのマスタを表すモデル。
 */
class News extends MasterModel
{
    /**
     * 日付として扱う属性。
     * @var array
     */
    protected $dates = [
        'open_at',
        'close_at',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'open_at' => 'timestamp',
        'close_at' => 'timestamp',
    ];
}
