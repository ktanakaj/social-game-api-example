<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

/**
 * マイグレーション用の共通処理。
 */
final class MigrationUtils
{
    /**
     * 最大の日時。
     * ※ DB上は9999年などもいけるが、PHP側で不都合がありそうだし当面不要なのでUNIXTIMEの範囲で設定。
     * @var string
     */
    private const MAX_DATE = '2038-01-01T00:00:00Z';

    /** new抑止用コンストラクタ。 */
    private function __construct()
    {
    }

    /**
     * 日時を使ったパーテイションを作成する。
     * @param string $table パーティションを作成するテーブル名。
     * @param string $column パーティション条件の列名。
     * @param string $conn DBコネクション。未指定時はデフォルト。
     */
    public static function createDatePartition(string $table, string $column, string $conn = null) : void
    {
        // サポートしていないDBでは無視する（主にユニットテストで違うDBを使う場合などを想定）
        $connection = DB::connection($conn);
        $driver = $connection->getDriverName();
        switch ($driver) {
            case 'mysql':
                self::createDatePartitionForMySQL($connection, $table, $column);
                break;
            default:
                \Log::debug("The {$driver} partition is not supported. Skipped.");
        }
    }

    /**
     * MySQLで日時を使ったパーテイションを作成する。
     * @param Connection $conn DBコネクション。
     * @param string $table パーティションを作成するテーブル名。
     * @param string $column パーティション条件の列名。
     */
    private static function createDatePartitionForMySQL(Connection $conn, string $table, string $column) : void
    {
        $max = new Carbon(self::MAX_DATE);
        $sql = "ALTER TABLE `{$table}` PARTITION BY RANGE COLUMNS(`{$column}`) (";
        for ($date = Carbon::today('UTC')->addMonth()->startOfMonth(); $date < $max; $date->addMonth()) {
            $sub = $date->copy()->subMonth();
            $sql .= "PARTITION p{$sub->format('Ym')} VALUES LESS THAN ('{$date->format('Y-m-d H:i:s')}'), ";
        }
        $sql .= "PARTITION pmax VALUES LESS THAN ('{$max->format('Y-m-d H:i:s')}'))";
        $conn->statement($sql);
    }

    /**
     * 主キーを変更する。
     * @param string $table 主キーを変更するテーブル名。
     * @param string|array $column 新しい主キー。複合主キーの場合は配列。
     * @param string $conn DBコネクション。未指定時はデフォルト。
     */
    public static function changePrimaryKey(string $table, $column, string $conn = null) : void
    {
        // DBの種類によって構文が違うので振り分け
        $connection = DB::connection($conn);
        $driver = $connection->getDriverName();
        switch ($driver) {
            case 'mysql':
                self::changePrimaryKeyForMySQL($connection, $table, $column);
                break;
            default:
                \Log::debug("The {$driver}'s primary key changing is not supported. Skipped.");
        }
    }

    /**
     * 主キーを変更する。
     * @param Connection $conn DBコネクション。
     * @param string $table 主キーを変更するテーブル名。
     * @param string|array $column 新しい主キー。複合主キーの場合は配列。
     */
    public static function changePrimaryKeyForMySQL(Connection $conn, string $table, $column) : void
    {
        $sql = "ALTER TABLE `{$table}` DROP PRIMARY KEY, ADD PRIMARY KEY (";
        if (is_array($column)) {
            foreach ($column as $i => $c) {
                if ($i > 0) {
                    $sql .= ', ';
                }
                $sql .= "`{$c}`";
            }
        } else {
            $sql .= "`{$column}`";
        }
        $sql .= ')';
        $conn->statement($sql);
    }
}
