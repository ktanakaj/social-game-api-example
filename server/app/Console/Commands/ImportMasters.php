<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enums\ExitCode;
use App\Models\Masters\MasterModel;

/**
 * マスタインポートコマンド。
 */
class ImportMasters extends Command
{
    /**
     * コンソールコマンドの名前と引数、オプション。
     * @var string
     */
    protected $signature = 'master:import {directory : Master files directory}';

    /**
     * コンソールコマンドの説明。
     * @var string
     */
    protected $description = 'Import master files to db';

    /**
     * コンソールコマンドの実行。
     * @return int 終了コード。
     */
    public function handle()
    {
        // 引数チェック
        $dir = $this->argument('directory');
        if (!is_dir($dir)) {
            $this->warn("\"{$dir}\" is not directory.");
            return ExitCode::DATAERR;
        }

        // マスタファイル一覧を取得
        $files = glob("{$dir}/*.csv");
        if (empty($files)) {
            $this->warn("\"{$dir}\" is empty.");
            return ExitCode::NOINPUT;
        }

        // マスタファイルをインポート
        foreach ($files as $file) {
            \DB::connection('master')->transaction(function () use ($file) {
                $this->importMaster($file);
            });
        }

        // マスタキャッシュをリセット
        // ※ 現状、マスタキャッシュ以外のキャッシュも全て消している
        \Cache::flush();
    }

    /**
     * CSVファイルからマスタをインポートする。
     * @param string $csvpath CSVファイルのパス。
     */
    private function importMaster(string $csvpath) : void
    {
        $this->info("{$csvpath} : importing...");

        // CSVファイルのファイル名をマスタ名とみなしてモデルクラス取得
        $name = basename($csvpath, '.csv');
        $classname = MasterModel::getMasterModel($name);
        if (!$classname) {
            $this->warn("{$name} is not found. skipped.");
            return;
        }

        // CSVのレコードを1件ずつモデルに変換して登録
        // ※ データ量が増えて遅くなったら100件単位などのbulkCreateを検討する
        $imported = 0;
        $rejected = 0;
        $csv = new \SplFileObject($csvpath);
        $csv->setFlags(\SplFileObject::READ_CSV);
        $classname::query()->delete();
        $headers = null;
        foreach ($csv as $row) {
            // 空行は除く
            if (empty($row) || empty($row[0])) {
                continue;
            }
            // 先頭行はヘッダーとして扱う、ヘッダーはモデルに合わせてスネークケースにする。
            // また、UTF-8のBOMが付いている場合除去する。
            if (empty($headers)) {
                $row[0] = self::trimBOM($row[0]);
                $headers = array_map('\Illuminate\Support\Str::snake', $row);
                continue;
            }
            try {
                $data = array_combine($headers, $row);
                if ($data === false) {
                    throw new \InvalidArgumentException('number of columns is not match with header');
                }
                $classname::create($data);
                ++$imported;
            } catch (\Exception $e) {
                $this->error($e->getMessage() . ' (' . json_encode($row) . ')');
                ++$rejected;
            }
        }

        if ($rejected > 0) {
            $this->error("{$csvpath} : {$imported} records were imported, {$rejected} records were rejected.");
        } else {
            $this->info("{$csvpath} : {$imported} records were imported.");
        }
    }

    /**
     * 文字列にBOM (byte order mark) が付いていたら除去する。
     * @param string $s BOMを除去する文字列。
     * @return string BOMを除去した文字列。
     */
    private static function trimBOM(string $s) : string
    {
        return substr($s, 0, 3) == "\xef\xbb\xbf" ? substr($s, 3) : $s;
    }
}
