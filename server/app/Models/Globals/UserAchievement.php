<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CamelcaseJson;
use App\Models\ObjectReceiver;
use App\Models\Virtual\ReceivedInfo;

/**
 * アチーブメントデータを表すモデル。
 *
 * アチーブメントデータとしては、「通常」「デイリー」「ウィークリー」
 * の現在値と報酬受取済かを保存する。
 * 「デイリー」「ウィークリー」の場合、過去のものは削除して再生成される。
 *
 * アチーブメントの現在値は一つのみ保持する。
 * 複数の条件を持つアチーブメント（例、ゴブリン10体とスライム10匹を倒せ）は想定しない。
 */
class UserAchievement extends Model
{
    use CamelcaseJson;

    /**
     * 複数代入する属性。
     * @var array
     */
    protected $fillable = [
        'user_id',
        'achievement_id',
        'score',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'achievement_id' => 'integer',
        'score' => 'integer',
        'received' => 'boolean',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'score' => 0,
        'received' => false,
    ];

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // アチーブメント全般でデフォルトのソート順を設定
        static::addGlobalScope('sortId', function (Builder $builder) {
            return $builder->orderBy('user_id', 'asc')->orderBy('achievement_id', 'asc');
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
