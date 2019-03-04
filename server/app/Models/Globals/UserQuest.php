<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CamelcaseJson;

/**
 * ユーザーのクエスト攻略状態を表すモデル。
 */
class UserQuest extends Model
{
    use CamelcaseJson;

    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'user_id',
        'quest_id',
        'count',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'quest_id' => 'integer',
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

        // クエスト全般でデフォルトのソート順を設定
        static::addGlobalScope('sortUsreAndQuest', function (Builder $builder) {
            return $builder->orderBy('user_id', 'asc')->orderBy('quest_id', 'asc');
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
     * クエストマスタとのリレーション定義。
     */
    public function quest() : BelongsTo
    {
        return $this->belongsTo('App\Models\Masters\Quest');
    }
}
