<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Globals\User;
use App\Models\Globals\UserCard;
use App\Models\Globals\UserItem;
use App\Models\Globals\UserGift;

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

        // ページング用クラスをアプリ用にカスタマイズしたものに差し替え
        $this->app->bind(
            'Illuminate\Pagination\Paginator',
            'App\Models\JsonPaginator'
        );
        $this->app->bind(
            'Illuminate\Pagination\LengthAwarePaginator',
            'App\Models\JsonLengthAwarePaginator'
        );

        // プレゼント受け取り処理の登録
        // TODO: 現状の実装だと、プレゼント一括受け取り時に何度もSELECT&UPDATEしてしまうので、
        //       ちゃんとやる場合はどこかにインスタンスをキャッシュして最後にUPDATEするようにする。
        // TODO: よく考えたらギフト以外（アイテムドロップとか）も全部同じはずなので、
        //       ReceivableObjectとか作ってそっちに集約する。
        UserGift::giftReceiver('gameCoin', [User::class, 'receiveGameCoinGift']);
        UserGift::giftReceiver('specialCoin', [User::class, 'receiveSpecialCoinGift']);
        UserGift::giftReceiver('exp', [User::class, 'receiveExpGift']);
        UserGift::giftReceiver('item', [UserItem::class, 'receiveItemGift']);
        UserGift::giftReceiver('card', [UserCard::class, 'receiveCardGift']);
    }
}
