<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CamelcaseJson;
use App\Models\Virtual\ReceivedObject;

/**
 * ユーザーが持つカードを表すモデル。
 *
 * ユーザーが保持するカードとその情報が格納される。
 */
class UserCard extends Model
{
    use CamelcaseJson;

    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'user_id',
        'card_id',
        'count',
        'exp',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'card_id' => 'integer',
        'count' => 'integer',
        'exp' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'count' => 1,
        'exp' => 0,
    ];

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // カード全般でデフォルトのソート順を設定
        static::addGlobalScope('sortUsreAndCard', function (Builder $builder) {
            return $builder->orderBy('user_id', 'asc')->orderBy('card_id', 'asc')->orderBy('id', 'asc');
        });
    }

    /**
     * ユーザーとのリレーション定義。
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo('App\Models\Globals\User');
    }

    /**
     * カードマスタとのリレーション定義。
     */
    public function card() : BelongsTo
    {
        return $this->belongsTo('App\Models\Masters\Card');
    }

    /**
     * デッキのカード情報とのリレーション定義。
     */
    public function decks() : HasMany
    {
        return $this->hasMany('App\Models\Globals\UserDeckCard');
    }

    /**
     * カードのプレゼントを受け取る。
     * @param UserGift $userGift カードのプレゼント。
     * @return ReceivedObject 受け取り情報。
     */
    public static function receiveCardGift(UserGift $userGift) : ReceivedObject
    {
        // TODO: bulkでやる
        // TODO: is_newの判定は、複数件受け取り時に何度もSELECTされるのでキャッシュとか検討
        $received = new ReceivedObject($userGift->toArray());
        $received->is_new = self::where('user_id', $userGift->user_id)->where('card_id', $userGift->object_id)->doesntExist();
        for ($i = 0; $i < $userGift->count; $i++) {
            self::create([
                'user_id' => $userGift->user_id,
                'card_id' => $userGift->object_id,
            ]);
        }
        return $received;
    }
}
