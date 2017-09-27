<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ユーザーの行動履歴を扱うモデル。
 */
class UserActionLog extends Model
{
    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'data' => '{}',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * `updated_at`の自動挿入の無効化。
     * @param mixed $value 更新日時。
     * @return UserGift $this
     */
    public function setUpdatedAt($value) : UserGift
    {
        return $this;
    }

    /**
     * ログのユーザーを取得する。
     * @return User ユーザー。
     */
    public function user() : User
    {
        return $this->belongsTo('App\Models\User');
    }
}
