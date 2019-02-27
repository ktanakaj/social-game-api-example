<?php

namespace App\Models\Globals;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\CamelcaseJson;
use App\Models\Masters\Level;
use App\Models\Masters\Parameter;
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
        'level' => 'integer',
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
        'level' => 1,
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

    /**
     * レベルを保存する。
     * @param mixed $value 値。
     */
    public function setLevelAttribute(int $value) : void
    {
        // レベルアップ分スタミナも回復する
        $stamina = 0;
        for ($i = $this->level + 1; $i <= $value; $i++) {
            $stamina += Level::findOrFail($i)->max_stamina;
        }
        if ($stamina > 0) {
            $this->stamina += $stamina;
        }
        $this->attributes['level'] = $value;
    }

    /**
     * 経験値を保存する。
     * @param mixed $value 値。
     */
    public function setExpAttribute(int $value) : void
    {
        // 経験値が保存されたタイミングで、レベルアップを判定する
        // ※ レベルマスタが変更され、計算上レベルが戻るような事があっても、自動で更新はしない。
        //    単に次のレベルまでが遠くなるだけ。
        $this->attributes['exp'] = $value;
        $level = Level::findByExp($value);
        if ($level->level > $this->level) {
            $this->level = $level->level;
        }
    }

    /**
     * スタミナを取得する。
     * @param int $value スタミナ元値。
     * @return int 計算されたスタミナ値。
     */
    public function getStaminaAttribute(int $value) : int
    {
        // 最終更新時間と現在日時の差から、スタミナを最大値まで回復させる。
        // ただし、初めから最大を超えている場合はその値を用いる。
        // また、マスタがオフられている場合は回復しない。
        $level = Level::findOrFail($this->level);
        $rate = Parameter::get('STAMINA_RECOVERY_RATE', 0);
        if ($value >= $level->max_stamina || $rate <= 0) {
            return $value;
        }
        if (!$this->stamina_updated_at) {
            return $level->max_stamina;
        }
        // ※ 一応、デバッグ機能等で現在日時が過去に戻ることも想定。減りはしない。
        $diff = floor(Carbon::now()->diffInMinutes(Carbon::createFromTimestamp($this->stamina_updated_at)) / $rate);
        if ($diff > 0) {
            $value += $diff;
        }
        return $value >= $level->max_stamina ? $level->max_stamina : $value;
    }

    /**
     * スタミナを保存する。
     * @param mixed $value 値。
     */
    public function setStaminaAttribute(int $value) : void
    {
        // スタミナが保存されたタイミングで、最終更新時間を更新する
        $this->attributes['stamina'] = $value;
        $this->stamina_updated_at = Carbon::now();
    }

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
