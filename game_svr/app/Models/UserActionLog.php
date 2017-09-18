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
     * ログのユーザーを取得する。
     * @return User ユーザー。
     */
    public function user() : User
    {
        return $this->belongsTo('App\Model\User');
    }
}
