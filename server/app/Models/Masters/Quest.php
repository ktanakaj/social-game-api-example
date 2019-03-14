<?php

namespace App\Models\Masters;

use Carbon\Carbon;
use App\Models\CamelcaseJson;

/**
 * クエストマスタを表すモデル。
 *
 * クエストはゲームのメインとなるインゲームの外枠に当たるもの。
 * クエスト名や消費スタミナなどを持ち、報酬やステージ情報などがクエストに紐づく。
 *
 * …が、このサンプルではインゲームの中身については現状扱っていないので、
 * 外枠部分の定義のみ実装されている。
 */
class Quest extends MasterModel
{
    use CamelcaseJson;

    /**
     * 日付として扱う属性。
     * @var array
     */
    protected $dates = [
        'open_at',
        'close_at',
    ];

    /**
     * ネイティブなタイプへキャストする属性。
     * @var array
     */
    protected $casts = [
        'previous_id' => 'integer',
        'open_at' => 'timestamp',
        'close_at' => 'timestamp',
        'stamina' => 'integer',
        'first_drop_set_id' => 'integer',
        'retry_drop_set_id' => 'integer',
    ];

    /**
     * 公開中マスタのみを取得するクエリスコープ。
     */
    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $now = Carbon::now();
            return $query->where(function ($query) use ($now) {
                $query->whereNull('open_at')->orWhere('open_at', '<=', $now);
            })->where(function ($query) use ($now) {
                $query->whereNull('close_at')->orWhere('close_at', '>=', $now);
            });
        });
    }
}
