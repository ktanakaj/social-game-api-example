<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\CamelcaseJson;
use App\Models\Virtual\ObjectInfo;
use App\Models\Virtual\ReceivedInfo;

/**
 * ユーザーデータを表すモデル。
 */
class User extends Authenticatable
{
    use SoftDeletes, CamelcaseJson;

    /**
     * 複数代入可能なプロパティ。
     * @var array
     */
    protected $fillable = [
        'name',
        'game_coins',
        'special_coins',
        'free_special_coins',
        'exp',
        'stamina',
    ];

    /**
     * JSONへの変換結果に含めないカラム。
     * @var array
     */
    protected $hidden = [
        'token',
        'stamina_updated_at',
    ];

    /**
     * 日付として扱う属性。
     * @var array
     */
    protected $dates = [
        'stamina_updated_at',
        'last_login',
        'deleted_at',
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
        'last_selected_deck_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
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
     * ユーザーのデッキとのリレーション定義。
     */
    public function decks() : HasMany
    {
        return $this->hasMany('App\Models\Globals\UserDeck');
    }

    // TODO: staminaをミューテタにして、時間経過で回復するようにする

    /**
     * ゲームコインを受け取る。
     * @param int $userId ユーザーID。
     * @param ObjectInfo $info 受け取るコイン情報。
     * @return ReceivedInfo 受け取り情報。
     */
    public static function receiveGameCoinTo(int $userId, ObjectInfo $info) : ReceivedInfo
    {
        $user = self::lockForUpdate()->findOrFail($userId);
        $user->game_coins += $info->count;
        $user->save();
        $received = new ReceivedInfo($info);
        $received->total = $user->game_coins;
        return $received;
    }

    /**
     * スペシャルコインを受け取る。
     * @param int $userId ユーザーID。
     * @param ObjectInfo $info 受け取るコイン情報。
     * @return ReceivedInfo 受け取り情報。
     */
    public static function receiveSpecialCoinTo(int $userId, ObjectInfo $info) : ReceivedInfo
    {
        // このメソッドで受け取った分は、非課金コインとして加算する
        $user = self::lockForUpdate()->findOrFail($userId);
        $user->free_special_coins += $info->count;
        $user->save();
        $received = new ReceivedInfo($info);
        $received->total = $user->special_coins + $user->free_special_coins;
        return $received;
    }

    /**
     * ユーザー経験値を受け取る。
     * @param int $userId ユーザーID。
     * @param ObjectInfo $info 受け取る経験値情報。
     * @return ReceivedInfo 受け取り情報。
     */
    public static function receiveExpTo(int $userId, ObjectInfo $info) : ReceivedInfo
    {
        $user = self::lockForUpdate()->findOrFail($userId);
        $user->exp += $info->count;
        $user->save();
        $received = new ReceivedInfo($info);
        $received->total = $user->exp;
        return $received;
    }
}
