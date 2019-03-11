<?php

namespace App\Models\Masters;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
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
    public function scopeActive(Builder $query) : Builder
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

    /**
     * 前クエストIDを保存する。
     * @param mixed $value 値。
     */
    public function setPreviousIdAttribute($value) : void
    {
        // マスタインポート用。CSVから空文字列が渡されるとエラーになるので、
        // nullに読み替え
        $this->attributes['previous_id'] = $value !== '' ? $value : null;
    }

    /**
     * 公開開始日時を保存する。
     * @param mixed $value 値。
     */
    public function setOpenAtAttribute($value) : void
    {
        // マスタインポート用。CSVから空文字列が渡されるとエラーになるので、
        // nullに読み替え
        $this->attributes['open_at'] = $value !== '' ? $value : null;
    }

    /**
     * 公開終了日時を保存する。
     * @param mixed $value 値。
     */
    public function setCloseAtAttribute($value) : void
    {
        // マスタインポート用。CSVから空文字列が渡されるとエラーになるので、
        // nullに読み替え
        $this->attributes['close_at'] = $value !== '' ? $value : null;
    }

    /**
     * 初回ドロップ報酬IDを保存する。
     * @param mixed $value 値。
     */
    public function setFirstDropSetIdAttribute($value) : void
    {
        // マスタインポート用。CSVから空文字列が渡されるとエラーになるので、
        // nullに読み替え
        $this->attributes['first_drop_set_id'] = $value !== '' ? $value : null;
    }

    /**
     * 周回ドロップ報酬IDを保存する。
     * @param mixed $value 値。
     */
    public function setRetryDropSetIdAttribute($value) : void
    {
        // マスタインポート用。CSVから空文字列が渡されるとエラーになるので、
        // nullに読み替え
        $this->attributes['retry_drop_set_id'] = $value !== '' ? $value : null;
    }
}
