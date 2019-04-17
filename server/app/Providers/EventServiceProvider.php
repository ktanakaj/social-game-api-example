<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot() : void
    {
        parent::boot();

        // 開発用のDBログ
        if (config('app.debug')) {
            \DB::listen(function ($ev) {
                // ※ bind変数は便宜上JSONエンコードして出力しているが、JSONシリアライズを実装したインスタンスを渡した場合、
                //    実際にSQLで使用される値と一致しないログが出ることもあるので、注意。
                \Log::debug("DB({$ev->connectionName}): {$ev->sql}; bindings=" . \json_encode($ev->connection->prepareBindings($ev->bindings)) . ' time=' . sprintf("%.2fms", $ev->time));
            });
            \Event::listen('Illuminate\Database\Events\TransactionBeginning', function ($ev) {
                \Log::debug("DB({$ev->connectionName}): start transaction");
            });
            \Event::listen('Illuminate\Database\Events\TransactionCommitted', function ($ev) {
                \Log::debug("DB({$ev->connectionName}): commit");
            });
            \Event::listen('Illuminate\Database\Events\TransactionRolledBack', function ($ev) {
                \Log::debug("DB({$ev->connectionName}): rollback");
            });
        }
    }
}
