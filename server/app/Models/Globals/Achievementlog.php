<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CamelcaseJson;

/**
 * ユーザーのアチーブメント達成履歴を表すモデル。
 *
 * KPI集計などの用途を想定。
 */
class Achievementlog extends Model
{
    use CamelcaseJson;

    /** update_atの無効化。 */
    const UPDATED_AT = null;

    /**
     * ページングのデフォルト件数。
     * @var int
     */
    protected $perPage = 20;

    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'user_id',
        'achievement_id',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'achievement_id' => 'integer',
        'created_at' => 'timestamp',
    ];

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // アチーブメント達成履歴全般でデフォルトのソート順を設定
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
     * アチーブメントマスタとのリレーション定義。
     */
    public function achievement() : BelongsTo
    {
        return $this->belongsTo('App\Models\Masters\Achievement');
    }
}
