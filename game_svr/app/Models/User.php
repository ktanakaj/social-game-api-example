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
        return $this->hasMany('App\Models\UserItem');
    }

    /**
     * プレゼントボックスを取得する。
     * @return array ユーザーギフト配列。
     */
    public function userGifts() : array
    {
        return $this->hasMany('App\Models\UserGift');
    }

    /**
     * 行動履歴を取得する。
     * @return array 行動履歴配列。
     */
    public function userActionLogs() : array
    {
        return $this->hasMany('App\Models\UserActionLog');
    }

    /**
     * オブジェクト種別からコインなどを加算する。
     * @param array $data {type,count} 形式の情報。
     * @return User 加算されたオブジェクト。対象外の種別の場合はnull。
     */
    public function addObjectByType(array $data) : ?User
    {
        switch ($data['type']) {
            case 'game_coin':
                $this->game_coin += $data['count'];
                return $this;
            case 'special_coin':
                $this->special_coin += $data['count'];
                return $this;
            case 'free_special_coin':
                $this->free_special_coin += $data['count'];
                return $this;
            case 'exp':
                $this->exp += $data['count'];
                return $this;
        }
        return null;
    }
}
