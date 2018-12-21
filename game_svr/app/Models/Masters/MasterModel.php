<?php

namespace App\Models\Masters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * マスタモデル抽象クラス。
 */
abstract class MasterModel extends Model
{
    /** デフォルトのキャッシュ保持期間（分） */
    protected const DEFAULT_TTL = 1440;

    /**
     * モデルで使用するコネクション名
     * @var string
     */
    protected $connection = 'master';

    /**
     * モデルのタイムスタンプを更新するかの指示。
     * @var bool
     */
    public $timestamps = false;

    /**
     * マスタを主キーで取得する。
     * @param mixed $id マスタの主キー。複数指定された場合、複数件を一括検索。
     * @param array $columns 取得するカラム。
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null マスタインスタンス。※キャッシュ有
     */
    public static function find($id, $columns = ['*'])
    {
        return Cache::remember(static::makeCacheKey('find', $id, $columns), static::DEFAULT_TTL, static function () use ($id, $columns) {
            // parentだと何故か呼べないので、parentの処理をコピーして対処
            if (is_array($id) || $id instanceof Arrayable) {
                return static::findMany($id, $columns);
            }
            return static::whereKey($id)->first($columns);
        });
    }

    /**
     * メソッドキャッシュのキーを生成する。
     * @param string $method メソッド名。
     * @param array $params メソッド引数。
     * @return string キー。
     */
    protected static function makeCacheKey(string $method, ...$params) : string
    {
        return get_called_class() . ':' . $method . ':' . json_encode($params);
    }
}
