<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Virtual\ReceivedObject;

/**
 * ユーザーデータを表すモデル。
 */
class User extends Authenticatable
{
    /**
     * 複数代入可能なプロパティ。
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * JSONへの変換結果に含めないカラム。
     * @var array
     */
    protected $hidden = [
        'token',
    ];

    /**
     * 日付として扱う属性。
     * @var array
     */
    protected $dates = [
        'stamina_updated_at',
        'last_login',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'game_coins' => 'integer',
        'special_coins' => 'integer',
        'free_special_coins' => 'integer',
        'exp' => 'integer',
        'stamina' => 'integer',
        'stamina_updated_at' => 'timestamp',
        'last_login' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'name' => '(noname)',
        'game_coins' => 0,
        'special_coins' => 0,
        'free_special_coins' => 0,
        'exp' => 0,
        'stamina' => 0,
    ];

    /**
     * プロパティに値を保存する。
     * @param string $key プロパティ名。
     * @param mixed $value 値。
     */
    public function setAttribute($key, $value) : void
    {
        // remember_token無効化のためのオーバーライド
        $isRememberTokenAttribute = $key == $this->getRememberTokenName();
        if (!$isRememberTokenAttribute) {
            parent::setAttribute($key, $value);
        }
    }

    /**
     * 認証用のパスワードを取得する。
     */
    public function getAuthPassword() : string
    {
        // 端末トークンをパスワードとして扱う
        return $this->token;
    }

    /**
     * ユーザーの所持品とのリレーション定義。
     */
    public function items() : HasMany
    {
        return $this->hasMany('App\Models\Globals\UserItem');
    }

    /**
     * ユーザーのカードとのリレーション定義。
     */
    public function cards() : HasMany
    {
        return $this->hasMany('App\Models\Globals\UserCard');
    }

    /**
     * ユーザーのプレゼントボックスとのリレーション定義。
     */
    public function gifts() : HasMany
    {
        return $this->hasMany('App\Models\Globals\UserGift');
    }

    /**
     * ゲームコインのプレゼントを受け取る。
     * @param UserGift $userGift ゲームコインのプレゼント。
     * @return ReceivedObject 受け取り情報。
     */
    public static function receiveGameCoinGift(UserGift $userGift) : ReceivedObject
    {
        $user = $userGift->user;
        $user->game_coins += $userGift->count;
        $user->save();
        $received = new ReceivedObject($userGift->toArray());
        $received->total = $user->game_coins;
        return $received;
    }

    /**
     * スペシャルコインのプレゼントを受け取る。
     * @param UserGift $userGift スペシャルコインのプレゼント。
     * @return ReceivedObject 受け取り情報。
     */
    public static function receiveSpecialCoinGift(UserGift $userGift) : ReceivedObject
    {
        // このメソッドで受け取った分は、非課金コインとして加算する
        $user = $userGift->user;
        $user->free_special_coins += $userGift->count;
        $user->save();
        $received = new ReceivedObject($userGift->toArray());
        $received->total = $user->special_coins + $user->free_special_coins;
        return $received;
    }

    /**
     * ユーザー経験値のプレゼントを受け取る。
     * @param UserGift $userGift ユーザー経験値のプレゼント。
     * @return ReceivedObject 受け取り情報。
     */
    public static function receiveExpGift(UserGift $userGift) : ReceivedObject
    {
        $user = $userGift->user;
        $user->exp += $userGift->count;
        $user->save();
        $received = new ReceivedObject($userGift->toArray());
        $received->total = $user->exp;
        return $received;
    }
}
