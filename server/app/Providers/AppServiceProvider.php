<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use App\Enums\EffectType;
use App\Enums\ObjectType;
use App\Models\Globals\User;
use App\Models\Globals\UserCard;
use App\Models\Globals\UserItem;
use App\Models\Effector;
use App\Models\ObjectReceiver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot() : void
    {
        // DBのインデックス長の設定
        \Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     */
    public function register() : void
    {
        // ページング用クラスをアプリ用にカスタマイズしたものに差し替え
        $this->app->bind(
            'Illuminate\Pagination\Paginator',
            'App\Models\JsonPaginator'
        );
        $this->app->bind(
            'Illuminate\Pagination\LengthAwarePaginator',
            'App\Models\JsonLengthAwarePaginator'
        );

        // 各種受け取り処理/エフェクト処理の登録
        // TODO: 現状の実装だと、一括受け取り時に何度もSELECT&UPDATEしてしまうので、
        //       ちゃんとやる場合はどこかにインスタンスをキャッシュして最後にUPDATEするようにする。
        ObjectReceiver::receiver(ObjectType::GAME_COIN, [User::class, 'receiveGameCoinTo']);
        ObjectReceiver::receiver(ObjectType::SPECIAL_COIN, [User::class, 'receiveSpecialCoinTo']);
        ObjectReceiver::receiver(ObjectType::EXP, [User::class, 'receiveExpTo']);
        ObjectReceiver::receiver(ObjectType::ITEM, [UserItem::class, 'receiveTo']);
        ObjectReceiver::receiver(ObjectType::CARD, [UserCard::class, 'receiveTo']);
        Effector::effector(EffectType::STAMINA, [User::class, 'effectStaminaTo']);
        Effector::effector(EffectType::EXP, [User::class, 'effectExpTo']);
    }
}
