<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CamelcaseJson;

/**
 * ユーザーのガチャ履歴を表すモデル。
 *
 * 履歴の閲覧や、排出率確認などの用途を想定。
 */
class Gachalog extends Model
{
    use CamelcaseJson;

    /** update_atの無効化。 */
    const UPDATED_AT = null;

    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'user_id',
        'gacha_id',
        'gacha_price_id',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'gacha_id' => 'integer',
        'gacha_price_id' => 'integer',
        'created_at' => 'timestamp',
    ];

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // ガチャ履歴全般でデフォルトのソート順を設定
        static::addGlobalScope('sortCreatedAt', function (Builder $builder) {
            return $builder->orderBy('user_id', 'asc')->orderBy('created_at', 'desc')->orderBy('id', 'desc');
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
     * ガチャマスタとのリレーション定義。
     */
    public function gacha() : BelongsTo
    {
        return $this->belongsTo('App\Models\Masters\Gacha');
    }

    /**
     * ガチャ排出物履歴とのリレーション定義。
     */
    public function drops() : HasMany
    {
        return $this->hasMany('App\Models\Globals\GachalogDrop');
    }
}
