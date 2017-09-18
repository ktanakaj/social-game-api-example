<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ユーザーが持つアイテムを表すモデル。
 *
 * ユーザーが保持するアイテムや装備品、素材などが格納される。
 * ユーザーは重量の限界までアイテムを持つことができる。
 * アイテムは、特別なデータを持たないスタッカブル品と、
 * 個別に管理されるジェネレーテッド品がある。
 */
class UserItem extends Model
{
    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'property_ids' => '[]',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'property_ids' => 'array',
    ];

    /**
     * アイテムを所有するユーザーを取得する。
     * @return User ユーザー。
     */
    public function user() : User
    {
        return $this->belongsTo('App\Model\User');
    }

    /**
     * アイテムのマスタを取得する。
     * @return Item アイテムマスタ。
     */
    public function item() : Item
    {
        return $this->belongsTo('App\Model\Item');
    }

    /**
     * アイテムプロパティのマスタを取得する。
     * @return array アイテムプロパティマスタ配列。
     */
    public function itemProperties() : array
    {
        return ItemProperty::whereIn('id', $this->propertyIds)->get();
    }
}
