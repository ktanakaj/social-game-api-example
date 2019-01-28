<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot() : void
    {
        // DBのインデックス長の設定
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     */
    public function register() : void
    {
        // 開発用のSQLログ
        \DB::listen(function ($query) {
            \Log::debug('SQL: ' . $query->sql . '; bindings=' . \json_encode($query->bindings) . ' time=' . sprintf("%.2fms", $query->time));
        });
    }
}
