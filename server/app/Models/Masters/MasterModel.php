<?php

namespace App\Models\Masters;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

/**
 * マスタモデル抽象クラス。
 */
abstract class MasterModel extends Model
{
    use Cachable;

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
     * マスタの全データを取得する。
     * ※ キャッシュ用オーバーライド。
     * @param array|mixed $columns 取得するカラム。
     * @return \Illuminate\Database\Eloquent\Collection|static[] マスタインスタンスコレクション。※キャッシュ有
     */
    public static function all($columns = ['*'])
    {
        // ※ laravel-model-cachingのメソッドを改造して、有効期限を指定するようにしたバージョン
        if (config('laravel-model-caching.disabled')) {
            return parent::all($columns);
        }

        $class = get_called_class();
        $instance = new $class;
        $tags = $instance->makeCacheTags();
        $key = $instance->makeCacheKey();

        return $instance->cache($tags)
            ->remember($key, config('cache.master_cache_expire'), function () use ($columns) {
                return parent::all($columns);
            });
    }

    /**
     * 新しいクエリビルダーを生成する。
     * ※ キャッシュ用オーバーライド。
     * @param \Illuminate\Database\Query\Builder $query 元となるクエリビルダー。
     * @return Builder 生成したクエリビルダー。
     */
    public function newEloquentBuilder($query)
    {
        // ※ laravel-model-cachingのメソッドを改造して、有効期限を指定するビルダーを使うようにしたバージョン
        if (!$this->isCachable()) {
            $this->isCachable = false;
            return new Builder($query);
        }
        return new MasterCachedBuilder($query);
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
