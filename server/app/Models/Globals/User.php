<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * ユーザーデータを表すモデル。
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * 複数代入可能なプロパティ。
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * 通常の取得結果に含めないカラム。
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 日付として扱う属性。
     * @var array
     */
    protected $dates = [
        'last_login',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'last_login' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * ユーザーの所持品とのリレーション定義。
     */
    public function items() : HasMany
    {
        return $this->hasMany('App\Models\Globals\UserItem');
    }

    /**
     * ユーザーのプレゼントボックスとのリレーション定義。
     */
    public function gifts() : HasMany
    {
        return $this->hasMany('App\Models\Globals\UserGift');
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
