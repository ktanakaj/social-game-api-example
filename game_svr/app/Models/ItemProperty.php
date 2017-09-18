<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * アイテムのプロパティのマスタを表すモデル。
 *
 * レアアイテムには追加でプロパティ値を持つものがある。
 * プロパティを持つ場合は、アイテムの効果にその値が加算される。
 */
class ItemProperty extends Model
{
    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'name' => '{}',
        'use_effect' => '{}',
        'equipping_effect' => '{}',
        'material_effect' => '{}',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'name' => 'array',
        'use_effect' => 'array',
        'equipping_effect' => 'array',
        'material_effect' => 'array',
    ];
}
