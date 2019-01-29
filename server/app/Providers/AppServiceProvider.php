<?php

namespace App\Providers;

use Carbon\Carbon;
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
        // Carbonのデフォルトフォーマットを設定
        // ※ SQLの引数にCarbonインスタンスをそのまま渡せるようになど。APIのフォーマットは別途対応
        Carbon::serializeUsing(function (Carbon $carbon) {
            return $carbon->toDateTimeString();
        });

        // 開発用のSQLログ
        if (config('app.debug')) {
            \DB::listen(function ($query) {
                \Log::debug('SQL: ' . $query->sql . '; bindings=' . \json_encode($query->bindings) . ' time=' . sprintf("%.2fms", $query->time));
            });
        }
    }
}
