<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Virtual\ReceivedObject;

/**
 * ユーザーが持つアイテムを表すモデル。
 *
 * ユーザーが保持するアイテムが格納される。
 */
class UserItem extends Model
{
    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'count',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'item_id' => 'integer',
        'count' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'count' => 0,
    ];

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // アイテム全般でデフォルトのソート順を設定
        static::addGlobalScope('sortUsreAndItem', function (Builder $builder) {
            return $builder->orderBy('user_id', 'asc')->orderBy('item_id', 'asc')->orderBy('id', 'asc');
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
     * アイテムマスタとのリレーション定義。
     */
    public function item() : BelongsTo
    {
        return $this->belongsTo('App\Models\Masters\Item');
    }

    /**
     * アイテムのプレゼントを受け取る。
     * @param UserGift $userGift アイテムのプレゼント。
     * @return ReceivedObject 受け取り情報。
     */
    public static function receiveItemGift(UserGift $userGift) : ReceivedObject
    {
        // TODO: is_new判定する
        $userItem = self::lockForUpdate()->firstOrNew([
            'user_id' => $userGift->user_id,
            'item_id' => $userGift->object_id,
        ]);
        $userItem->count += $userGift->count;
        $userItem->save();
        $received = new ReceivedObject($userGift->toArray());
        $received->total = $userItem->count;
        return $received;
    }
}
