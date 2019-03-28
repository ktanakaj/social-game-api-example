<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ExitCode;
use App\Models\MigrationUtils;

/**
 * 月単位のDBパーティションのDROPコマンド。
 *
 * DBの履歴系テーブルから古いパーティションをDROPする。
 */
class DropMonthlyPartitions extends Command
{
    /**
     * コンソールコマンドの名前と引数、オプション。
     * @var string
     */
    protected $signature = 'partition:drop {year : Year} {month : Month} {classname : Model Class Name}';

    /**
     * コンソールコマンドの説明。
     * @var string
     */
    protected $description = 'Drop partition from db';

    /**
     * コンソールコマンドの実行。
     * @return int 終了コード。
     */
    public function handle()
    {
        $year = $this->argument('year');
        $month = $this->argument('month');
        $classname = $this->argument('classname');

        if (!class_exists($classname) || !is_subclass_of($classname, Model::class)) {
            $this->warn("\"{$classname}\" is not Model class.");
            return ExitCode::DATAERR;
        }

        $model = new $classname;
        $this->info("Drop monthlly partition table={$model->getTable()} date={$year}/{$month} : droping...");
        if (MigrationUtils::dropMonthlyPartition($model->getTable(), Carbon::create($year, $month), $model->getConnectionName())) {
            $this->info("Drop monthlly partition table={$model->getTable()} date={$year}/{$month} : dropped.");
        } else {
            $this->warn("The partition is not found. skipped.");
        }
    }
}
