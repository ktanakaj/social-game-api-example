<?php

namespace App\Models\Globals;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\AchievementType;
use App\Exceptions\BadRequestException;
use App\Models\CamelcaseJson;
use App\Models\ObjectReceiver;
use App\Models\Masters\Achievement;
use App\Models\Virtual\ReceivedInfo;

/**
 * ユーザーのアチーブメントデータを表すモデル。
 *
 * アチーブメントデータとしては、「通常」「デイリー」「ウィークリー」
 * の現在値と報酬受取済かを保存する。
 * 「デイリー」「ウィークリー」の場合、期限切れのレコードはリセットして再使用される。
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

        // アチーブメント達成時に、達成履歴を登録
        self::saving(function ($userAchievement) {
            // ※ 達成の判定は受け取りで実施（達成したけど受け取っていないケースは無視）
            if ($userAchievement->received && !$userAchievement->original['received']) {
                Achievementlog::create($userAchievement->toArray());
            }
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

    /**
     * スコアを保存する。
     * @param mixed $value 値。
     */
    public function setScoreAttribute(int $value) : void
    {
        // 最大スコアで判定するので、値が減るような更新は保存しない。また最大値を超える場合は最大値に切り捨て。
        // （後者は入れてもいいが、中途半端なデータが見えると変なので。）
        if ($value < $this->attributes['score']) {
            return;
        }
        $this->attributes['score'] = $value > $this->achievement->score ? $this->achievement->score : $value;
    }

    /**
     * アチーブメント達成済みか？
     * @return bool 達成済みの場合true。
     */
    public function isAchieved() : bool
    {
        return $this->score >= $this->achievement->score;
    }

    /**
     * アチーブメントは期限切れか？
     * @return bool 期限切れの場合true。
     */
    public function isExpired(): bool
    {
        // マスタが期限切れか、デイリー/ウィークリーで期間終了
        if (!$this->achievement->isActive()) {
            return false;
        }
        switch ($this->achievement->type) {
            case AchievementType::DAILY:
                return Carbon::createFromTimestamp($this->updated_at)->isToday();
            case AchievementType::WEEKLY:
                return Carbon::createFromTimestamp($this->updated_at)->isCurrentWeek();
            default:
                return true;
        }
    }

    /**
     * 達成済みアチーブメントの報酬を受け取る。
     * @return ReceivedInfo アチーブメント報酬の受け取り結果。
     */
    public function receive() : ReceivedInfo
    {
        if ($this->received || !$this->isExpired() || !$this->isAchieved()) {
            throw new BadRequestException("id={$this->id} can't be received");
        }
        $result = ObjectReceiver::receive($this->user_id, $this->achievement);
        $this->received = true;
        $this->save();
        return $result;
    }

    /**
     * 現在有効なアチーブメント一覧を取得する。存在しない場合新規作成する。
     * @param int $userId ユーザーID。
     * @param string $condition アチーブメントを条件で絞り込む場合その種別。
     * @return Collection UserAchievementコレクション。
     */
    public static function findActiveOrNew(int $userId, string $condition = null) : Collection
    {
        // マスタを元に現在有効なアチーブメントの一覧を取得する。
        // デイリーやウィークリーの古いアチーブメントはこのタイミングでリセットする。
        $query = Achievement::query();
        if ($condition) {
            $query = $query->where('condition', $condition);
        }
        $all = $query->get()->active();
        $userAchievementByIds = self::lockForUpdate()->where('user_id', $userId)->whereIn('achievement_id', $all->pluck('id'))->get()->keyBy('achievement_id');
        foreach ($all as $achievement) {
            if ($userAchievement = $userAchievementByIds->get($achievement->id)) {
                // ※ デイリー/ウィークリーを判定していないが、通常アチーブメントの期限切れは↑のactiveで除かれている想定
                if ($userAchievement->isExpired()) {
                    $userAchievement->score = 0;
                    $userAchievement->received = false;
                }
            } else {
                $userAchievementByIds->put($achievement->id, new UserAchievement(['user_id' => $userId, 'achievement_id' => $achievement->id]));
            }
        }
        return $userAchievementByIds->values();
    }
}
