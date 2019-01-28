<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * マイグレーション用の共通処理。
 */
class MigrationUtils
{
    /**
     * 最大の日時。
     * ※ DB上は9999年などもいけるが、PHP側で不都合がありそうだし当面不要なのでUNIXTIMEの範囲で設定。
     * @var string
     */
    private const MAX_DATE = '2038-01-01T00:00:00Z';

    /** new抑止用コンストラクタ。 */
    private function __construct() { }

    /**
     * 日時を使ったパーテイションを作成する。
     * @param ConnectionInterface $conn DBコネクション。
     * @param string $table パーティションを作成するテーブル名。
     * @param string $column パーティション条件の列名。
     */
    public static function createDatePartition(string $table, string $column, string $conn = null) : void
    {
        // TODO: デバッグ用に、MySQL以外ではログを出して無視する
        $max = new Carbon(self::MAX_DATE);
        $sql = "ALTER TABLE `{$table}` PARTITION BY RANGE COLUMNS(`{$column}`) (";
        for ($date = Carbon::today('UTC')->addMonth()->startOfMonth(); $date < $max; $date->addMonth()) {
            $sub = $date->copy()->subMonth();
            $sql .= "PARTITION p{$sub->format('Ym')} VALUES LESS THAN ('{$date->format('Y-m-d H:i:s')}'), ";
        }
        $sql .= "PARTITION pmax VALUES LESS THAN ('{$max->format('Y-m-d H:i:s')}'))";
        DB::connection($conn)->statement($sql);
    }
}
