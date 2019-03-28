<?php

namespace App\Models;

use Carbon\Carbon;

/**
 * 有効期間を持つモデルを扱うためのTrait。
 *
 * 有効期間は OPEN_AT, CLOSE_AT プロパティで定義。
 * 期間は「OPEN_AT以上」「CLOSE_AT以下」で判定する。
 * 期間が null の場合は無期限と判定。
 */
trait HasPeriod
{
    /**
     * 有効なモデルのみを取得するクエリスコープ。
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder クエリー。
     * @param \DateTimeInterface $date 現在日時ではなく指定された日時で判定する場合その値。
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder スコープを追加したクエリー。
     */
    public function scopeActive($query, \DateTimeInterface $date = null)
    {
        $date = $date ?? Carbon::now();
        return $query->where(function ($query) use ($date) {
            $openAtColumn = $this->getOpenAtColumn();
            $closeAtColumn = $this->getCloseAtColumn();

            if ($openAtColumn) {
                $query = $query->where(function ($query) use ($openAtColumn, $date) {
                    $query->whereNull($openAtColumn)->orWhere($openAtColumn, '<=', $date);
                });
            }

            if ($closeAtColumn) {
                $query = $query->where(function ($query) use ($closeAtColumn, $date) {
                    $query->whereNull($closeAtColumn)->orWhere($closeAtColumn, '>=', $date);
                });
            }

            return $query;
        });
    }

    /**
     * 有効期間の開始列名を取得する。
     * @return string 列名。デフォルトは open_at。nullの場合開始列無し。
     */
    private function getOpenAtColumn() : string
    {
        return defined('static::OPEN_AT') ? static::OPEN_AT : 'open_at';
    }

    /**
     * 有効期間の終了列名を取得する。
     * @return string 列名。デフォルトは close_at。nullの場合終了列無し。
     */
    private function getCloseAtColumn() : string
    {
        return defined('static::CLOSE_AT') ? static::CLOSE_AT : 'close_at';
    }
}
