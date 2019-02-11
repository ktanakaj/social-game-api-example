<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ユーザーが持つカードを表すモデル。
 *
 * ユーザーが保持するカードとその情報が格納される。
 */
class UserCard extends Model
{
    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'user_id',
        'card_id',
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
}
