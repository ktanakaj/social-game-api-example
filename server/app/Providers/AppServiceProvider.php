<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Enums\ObjectType;
use App\Models\Globals\User;
use App\Models\Globals\UserCard;
use App\Models\Globals\UserItem;
use App\Models\ObjectReceiver;

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
            return $carbon->copy()->setTimezone(config('app.timezone'))->toDateTimeString();
        });

        // 開発用のSQLログ
        if (config('app.debug')) {
            \DB::listen(function ($query) {
                \Log::debug('SQL: ' . $query->sql . '; bindings=' . \json_encode($query->bindings) . ' time=' . sprintf("%.2fms", $query->time));
            });
        }

        // ページング用クラスをアプリ用にカスタマイズしたものに差し替え
        $this->app->bind(
            'Illuminate\Pagination\Paginator',
            'App\Models\JsonPaginator'
        );
        $this->app->bind(
            'Illuminate\Pagination\LengthAwarePaginator',
            'App\Models\JsonLengthAwarePaginator'
        );

        // 各種受け取り処理の登録
        // TODO: 現状の実装だと、一括受け取り時に何度もSELECT&UPDATEしてしまうので、
        //       ちゃんとやる場合はどこかにインスタンスをキャッシュして最後にUPDATEするようにする。
        ObjectReceiver::receiver(ObjectType::GAME_COIN, [User::class, 'receiveGameCoinTo']);
        ObjectReceiver::receiver(ObjectType::SPECIAL_COIN, [User::class, 'receiveSpecialCoinTo']);
        ObjectReceiver::receiver(ObjectType::EXP, [User::class, 'receiveExpTo']);
        ObjectReceiver::receiver(ObjectType::ITEM, [UserItem::class, 'receiveTo']);
        ObjectReceiver::receiver(ObjectType::CARD, [UserCard::class, 'receiveTo']);
    }
}
