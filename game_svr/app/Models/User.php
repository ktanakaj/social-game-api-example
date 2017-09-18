<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * ユーザーデータを表すモデル。
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 所持品を取得する。
     * @return array ユーザーアイテム配列。
     */
    public function userItems() : array
    {
        return $this->hasMany('App\Model\UserItem');
    }

    /**
     * プレゼントボックスを取得する。
     * @return array ユーザーギフト配列。
     */
    public function userGifts() : array
    {
        return $this->hasMany('App\Model\UserGift');
    }

    /**
     * 行動履歴を取得する。
     * @return array 行動履歴配列。
     */
    public function userActionLogs() : array
    {
        return $this->hasMany('App\Model\UserActionLog');
    }
}
