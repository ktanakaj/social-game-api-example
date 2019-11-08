<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Connection;

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
     * 日時列を使った月単位のパーテイションを作成する。
     * @param string $table パーティションを作成するテーブル名。
     * @param string $column パーティション条件の列名。
     * @param string $conn DBコネクション。未指定時はデフォルト。
     */
    public static function createMonthlyPartitions(string $table, string $column, string $conn = null) : void
    {
        // サポートしていないDBでは無視する（主にユニットテストで違うDBを使う場合などを想定）
        $connection = \DB::connection($conn);
        $driver = $connection->getDriverName();
        switch ($driver) {
            case 'mysql':
                self::createMonthlyPartitionsForMySQL($connection, $table, $column);
                break;
            default:
                \Log::debug("The {$driver} partition is not supported. Skipped.");
        }
    }

    /**
     * MySQLで日時列を使った月単位のパーテイションを作成する。
     * @param Connection $conn DBコネクション。
     * @param string $table パーティションを作成するテーブル名。
     * @param string $column パーティション条件の列名。
     */
    private static function createMonthlyPartitionsForMySQL(Connection $conn, string $table, string $column) : void
    {
        $max = new Carbon(self::MAX_DATE);
        $sql = "alter table `{$table}` partition by range columns(`{$column}`) (";
        for ($date = Carbon::today('UTC')->addMonthsNoOverflow()->startOfMonth(); $date < $max; $date->addMonthsNoOverflow()) {
            // ※ p201903パーティションには2019年3月末までのデータが入るイメージ
            $sql .= "partition " . self::makeMonthlyPartitionName($date->copy()->subMonth()) . " values less than ('{$date->format('Y-m-d H:i:s')}'), ";
        }
        $sql .= "partition pmax values less than ('{$max->format('Y-m-d H:i:s')}'))";
        $conn->statement($sql);
    }

    /**
     * 月単位のパーテイションを削除する。
     * @param string $table パーティションを削除するテーブル名。
     * @param \DateTimeInterface $date 削除する月。
     * @param string $conn DBコネクション。未指定時はデフォルト。
     * @return bool 削除した場合true。
     */
    public static function dropMonthlyPartition(string $table, \DateTimeInterface $date, string $conn = null) : bool
    {
        // サポートしていないDBでは無視する（主にユニットテストで違うDBを使う場合などを想定）
        $connection = \DB::connection($conn);
        $driver = $connection->getDriverName();
        switch ($driver) {
            case 'mysql':
                return self::dropPartitionForMySQL($connection, $table, self::makeMonthlyPartitionName($date));
            default:
                \Log::debug("The {$driver} partition is not supported. Skipped.");
                return false;
        }
    }

    /**
     * MySQLのパーテイションを削除する。
     * @param Connection $conn DBコネクション。
     * @param string $table パーティションを削除するテーブル名。
     * @param string $partition パーティション名。
     * @return bool 削除した場合true。
     */
    private static function dropPartitionForMySQL(Connection $conn, string $table, string $partition) : bool
    {
        if (!self::existsPartitionForMySQL($conn, $table, $partition)) {
            return false;
        }
        $conn->statement("alter table `{$table}` drop partition {$partition}");
        return true;
    }

    /**
     * MySQLでDBにパーテイションが存在するか？
     * @param Connection $conn DBコネクション。
     * @param string $table パーティションを確認するテーブル名。
     * @param string $partition パーティション名。
     * @return bool 存在する場合true。
     */
    private static function existsPartitionForMySQL(Connection $conn, string $table, string $partition) : bool
    {
        return $conn->table('information_schema.partitions')->where('table_schema', $conn->getDatabaseName())->where('table_name', $table)->where('partition_name', $partition)->exists();
    }

    /**
     * 日時ベースの月単位のパーテイション名を生成する。
     * @param \DateTimeInterface $date 基点となる日時。
     */
    private static function makeMonthlyPartitionName(\DateTimeInterface $date) : string
    {
        return 'p' . $date->format('Ym');
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
        $connection = \DB::connection($conn);
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
        $sql = "alter table `{$table}` drop primary key, add primary key (";
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
