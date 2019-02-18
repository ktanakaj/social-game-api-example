<?php

namespace App\Models;

use Illuminate\Pagination\Paginator;

/**
 * JSON API向けに拡張したPaginator。
 */
class JsonPaginator extends Paginator
{
    /**
     * インスタンスを連想配列に変換する。
     */
    public function toArray() : array
    {
        // JSON APIでは不要なプロパティをカット&文字列が入りやすい項目をキャスト
        return [
            'perPage' => intval($this->perPage()),
            'currentPage' => $this->currentPage(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'data' => $this->items->toArray(),
        ];
    }
}
