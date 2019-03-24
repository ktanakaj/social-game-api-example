<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ItemType;
use App\Enums\ObjectType;
use App\Exceptions\BadRequestException;
use App\Exceptions\EmptyResourceException;
use App\Models\CamelcaseJson;
use App\Models\Effector;
use App\Models\Virtual\ObjectInfo;
use App\Models\Virtual\ReceivedInfo;

/**
 * ユーザーが持つアイテムを表すモデル。
 *
 * ユーザーが保持するアイテムが格納される。
 */
class UserItem extends Model
{
    use CamelcaseJson;

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
            return $builder->orderBy('user_id', 'asc')->orderBy('item_id', 'asc');
        });
    }

    /**
     * 0個を除外するクエリスコープ。
     */
    public function scopeNotEmpty(Builder $query) : Builder
    {
        return $query->where('count', '>', 0);
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
     * 所持数を保存する。
     * @param mixed $value 値。
     * @throws EmptyResourceException 所持数が足りない場合。
     */
    public function setCountAttribute(int $value) : void
    {
        // 保存前にバリデーション実施
        if ($value < 0) {
            throw new EmptyResourceException("count = {$value} is invalid", new ObjectInfo(['type' => ObjectType::ITEM, 'id' => $this->item_id, 'count' => abs($value)]));
        }
        $this->attributes['count'] = $value;
    }

    /**
     * 消費系アイテムを使用する。
     * @return array 使用結果のReceivedInfo配列。
     */
    public function use() : array
    {
        if ($this->item->type !== ItemType::USABLE) {
            throw new BadRequestException("id={$this->id} is not usable");
        }
        --$this->count;
        return Effector::effect($this->user_id, $this->item->effect);
    }

    /**
     * アイテムを受け取る。
     * @param int $userId ユーザーID。
     * @param ObjectInfo $info 受け取るアイテム情報。
     * @return ReceivedInfo 受け取り情報。
     */
    public static function receiveTo(int $userId, ObjectInfo $info) : ReceivedInfo
    {
        $received = new ReceivedInfo($info);
        $userItem = self::lockForUpdate()->firstOrNew(['user_id' => $userId, 'item_id' => $info->id]);
        $userItem->count += $info->count;
        $received->total = $userItem->count;
        $received->is_new = !$userItem->exists;
        $userItem->save();
        return $received;
    }
}
