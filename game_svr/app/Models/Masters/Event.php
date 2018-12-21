<?php

namespace App\Models\Masters;

/**
 * イベントのマスタを表すモデル。
 *
 * 各種イベントのベースとなるマスタ。
 * このマスタが個別のイベントマスタのIDマスタとなる。
 */
class Event extends MasterModel
{
    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'title' => '{}',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'title' => 'array',
    ];
}
