<?php

namespace App\Models\Globals;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\QuestStatus;
use App\Events\QuestlogSaved;
use App\Models\CamelcaseJson;

/**
 * ユーザーのクエストプレイ履歴を表すモデル。
 *
 * 中断したバトルへの復帰や、KPI集計などの用途を想定。
 */
class Questlog extends Model
{
    use CamelcaseJson;

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
        'quest_id',
        'status',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'quest_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];

    /**
     * 属性に設定するデフォルト値。
     * @var array
     */
    protected $attributes = [
        'status' => QuestStatus::STARTED,
    ];

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // クエスト履歴全般でデフォルトのソート順を設定
        static::addGlobalScope('sortCreatedAt', function (Builder $builder) {
            return $builder->orderBy('user_id', 'asc')->orderBy('created_at', 'desc')->orderBy('id', 'desc');
        });

        // 更新イベントを登録
        self::saving(function ($log) {
            event(new QuestlogSaved($log, $log->original));
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
