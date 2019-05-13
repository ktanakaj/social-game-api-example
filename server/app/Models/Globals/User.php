<?php

namespace App\Models\Globals;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Enums\ObjectType;
use App\Exceptions\EmptyResourceException;
use App\Models\CamelcaseJson;
use App\Models\Effector;
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
        'paid_special_coins',
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
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'game_coins' => 'integer',
        'special_coins' => 'integer',
        'paid_special_coins' => 'integer',
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
        'paid_special_coins' => 0,
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
     * ユーザーのクエスト攻略状態とのリレーション定義。
     */
    public function quests() : HasMany
    {
        return $this->hasMany('App\Models\Globals\UserQuest');
    }

    /**
     * ユーザーのアチーブメント達成状況とのリレーション定義。
     */
    public function achievements() : HasMany
    {
        return $this->hasMany('App\Models\Globals\UserAchievement');
    }

    /**
     * ユーザーのクエスト履歴とのリレーション定義。
     */
    public function questlogs() : HasMany
    {
        return $this->hasMany('App\Models\Globals\Questlog');
    }

    /**
     * ユーザーのアチーブメント達成履歴とのリレーション定義。
     */
    public function achievementlogs() : HasMany
    {
        return $this->hasMany('App\Models\Globals\Achievementlog');
    }

    /**
     * ユーザーのガチャ履歴とのリレーション定義。
     */
    public function gachalogs() : HasMany
    {
        return $this->hasMany('App\Models\Globals\Gachalog');
    }

    /**
     * ゲームコインを保存する。
     * @param mixed $value 値。
     * @throws EmptyResourceException ゲームコインが足りない場合。
     */
    public function setGameCoinsAttribute(int $value) : void
    {
        // 保存前にバリデーション実施
        if ($value < 0) {
            throw new EmptyResourceException("game_coins = {$value} is invalid", new ObjectInfo(['type' => ObjectType::GAME_COIN, 'count' => abs($value)]));
        }
        $this->attributes['game_coins'] = $value;
    }

    /**
     * スペシャルコインを保存する。
     * @param mixed $value 値。
     * @throws EmptyResourceException スペシャルコインが足りない場合。
     */
    public function setSpecialCoinsAttribute(int $value) : void
    {
        // 保存前にバリデーション実施
        if ($value < 0) {
            // 減らす場合は、有償分があればそちらから減らして補う
            if ($this->paid_special_coins + $value < 0) {
                throw new EmptyResourceException("special_coins = {$value} is invalid", new ObjectInfo(['type' => ObjectType::SPECIAL_COIN, 'count' => abs($value)]));
            }
            $this->paid_special_coins += $value;
            $value = 0;
        }
        $this->attributes['special_coins'] = $value;
    }

    /**
     * スペシャルコイン（有償）を保存する。
     * @param mixed $value 値。
     * @throws EmptyResourceException スペシャルコインが足りない場合。
     */
    public function setPaidSpecialCoinsAttribute(int $value) : void
    {
        // 保存前にバリデーション実施
        if ($value < 0) {
            throw new EmptyResourceException("paid_special_coins = {$value} is invalid", new ObjectInfo(['type' => ObjectType::PAID_SPECIAL_COIN, 'count' => abs($value)]));
        }
        $this->attributes['paid_special_coins'] = $value;
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
     * @throws EmptyResourceException スタミナが足りない場合。
     */
    public function setStaminaAttribute(int $value) : void
    {
        // 保存前にバリデーション実施
        if ($value < 0) {
            throw new EmptyResourceException("stamina = {$value} is invalid", new ObjectInfo(['type' => ObjectType::STAMINA, 'count' => abs($value)]));
        }

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
        // このメソッドで受け取った分は、無償コインとして加算する
        $user = self::lockForUpdate()->findOrFail($userId);
        $user->special_coins += $info->count;
        $user->save();
        $received = new ReceivedInfo($info);
        $received->total = $user->special_coins + $user->paid_special_coins;
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

    /**
     * スタミナのエフェクトを適用する。
     * @param int $userId ユーザーID。
     * @param string $effectValue エフェクト設定値。
     * @return ReceivedInfo 適用結果。
     */
    public static function effectStaminaTo(int $userId, string $effectValue) : ReceivedInfo
    {
        // スタミナ回復薬+50などを想定。%での回復は現状未対応
        $user = self::lockForUpdate()->findOrFail($userId);
        $value = Effector::calcEffect($user->stamina, $effectValue);
        $received = new ReceivedInfo(['type' => ObjectType::STAMINA, 'count' => $value - $user->stamina, 'total' => $value]);
        $user->stamina = $value;
        $user->save();
        return $received;
    }

    /**
     * ユーザー経験値のエフェクトを適用する。
     * @param int $userId ユーザーID。
     * @param string $effectValue エフェクト設定値。
     * @return ReceivedInfo 適用結果。
     */
    public static function effectExpTo(int $userId, string $effectValue) : ReceivedInfo
    {
        // 経験値+50などを想定
        $user = self::lockForUpdate()->findOrFail($userId);
        $value = Effector::calcEffect($user->exp, $effectValue);
        $received = new ReceivedInfo(['type' => ObjectType::EXP, 'count' => $value - $user->exp, 'total' => $value]);
        $user->exp = $value;
        $user->save();
        return $received;
    }
}
