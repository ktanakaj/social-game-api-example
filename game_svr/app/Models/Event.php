<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * イベントのマスタを表すモデル。
 *
 * 各種イベントのベースとなるマスタ。
 * このマスタが個別のイベントマスタのIDマスタとなる。
 */
class Event extends Model
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
