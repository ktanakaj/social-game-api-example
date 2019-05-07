<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use GeneaLabs\LaravelModelCaching\CachedBuilder;

/**
 * マスタキャッシュ用にカスタマイズしたCachedBuilderクラス。
 */
class MasterCachedBuilder extends CachedBuilder
{
    /**
     * キャッシュ付きでクエリを実行する。
     * 実行結果はキャッシュされる。
     * 既にキャッシュに結果がある場合は、実行せずその値を返す。
     */
    protected function retrieveCachedValue(
        array $arguments,
        string $cacheKey,
        array $cacheTags,
        string $hashedCacheKey,
        string $method
    ) {
        // ※ laravel-model-cachingのメソッドを改造して、有効期限を指定するようにしたバージョン
        $this->checkCooldownAndRemoveIfExpired($this->model);

        return $this->cache($cacheTags)
            ->remember(
                $hashedCacheKey,
                config('cache.master_cache_expire'),
                function () use ($arguments, $cacheKey, $method) {
                    return [
                        "key" => $cacheKey,
                        "value" => EloquentBuilder::{$method}(...$arguments),
                    ];
                }
            );
    }
}
