<?php

namespace App\Models\Masters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
     * 主キーがインクリメントされるかの指示。
     * @var bool
     */
    public $incrementing = false;

    /**
     * 複数代入しない属性。
     * ※ マスタインポートのため全フィールド許可。
     * @var array
     */
    protected $guarded = [];

    /**
     * モデルの初期化。
     */
    protected static function boot() : void
    {
        parent::boot();

        // マスタはデフォルトではIDの昇順でソートする
        static::addGlobalScope('sortId', function (Builder $builder) {
            return $builder->orderBy('id', 'asc');
        });
    }

    /**
     * プロパティに値を保存する。
     * @param string $key プロパティ名。
     * @param mixed $value 値。
     */
    public function setAttribute($key, $value) : void
    {
        // CSV等からマスタをインポートする都合上、数値型や日付型の項目に空文字列が来て
        // エラーになることがあるので、その場合は空文字列をnull扱いにする。
        if ($value === '' && $this->hasCast($key) && $this->getCastType($key) !== 'string') {
            $value = null;
        }
        parent::setAttribute($key, $value);
    }

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

    /**
     * モデル名やテーブル名から、モデルクラスを取得する。
     * @param string $name モデル名 or テーブル名。
     * @return string モデルクラス名。存在しない場合null。
     */
    public static function getMasterModel(string $name) : ?string
    {
        $class = (new \ReflectionClass(self::class));
        $classname = $class->getNamespaceName() . '\\' . str_singular(studly_case($name));
        if (!class_exists($classname) || !is_subclass_of($classname, self::class)) {
            return null;
        }
        return $classname;
    }

    /**
     * マスタモデルクラスの一覧を取得する。
     * @return Collection モデルクラス名コレクション。
     */
    public static function getMasterModels() : Collection
    {
        // このディレクトリに存在するファイル名を元に、クラスを探索する
        // （アプリが複雑化してディレクトリが分割されるようなら別の方法を検討）
        $class = new \ReflectionClass(self::class);
        $dir = dirname($class->getFileName());
        $namespace = $class->getNamespaceName() . '\\';

        $models = [];
        foreach (glob("{$dir}/*.php") as $file) {
            $classname = $namespace . basename($file, '.php');
            if (class_exists($classname) && is_subclass_of($classname, self::class)) {
                $models[] = $classname;
            };
        }
        return collect($models)->sort()->values();
    }
}
