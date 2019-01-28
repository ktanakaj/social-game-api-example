<?php

namespace App\Models\Masters;

/**
 * アイテムマスタを表すモデル。
 *
 * アイテムには、消費アイテムと装備、素材などが含まれる。
 */
class Item extends MasterModel
{
    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'name' => '{}',
        'flavor' => '{}',
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
        'flavor' => 'array',
        'use_effect' => 'array',
        'equipping_effect' => 'array',
        'material_effect' => 'array',
    ];
}
