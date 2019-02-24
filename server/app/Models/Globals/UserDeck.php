<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CamelcaseJson;

/**
 * ユーザーが構築したデッキを表すモデル。
 *
 * デッキには複数のカードが所属する。
 */
class UserDeck extends Model
{
    use CamelcaseJson;

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'no' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // デッキ全般でデフォルトのソート順を設定
        static::addGlobalScope('sortUsreAndNo', function (Builder $builder) {
            return $builder->orderBy('user_id', 'asc')->orderBy('no', 'asc');
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
     * デッキのカード情報とのリレーション定義。
     */
    public function cards() : HasMany
    {
        return $this->hasMany('App\Models\Globals\UserDeckCard');
    }
}
