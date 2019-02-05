<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
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

        // 開発用のトランザクションログ
        if (config('app.debug')) {
            Event::listen('Illuminate\Database\Events\TransactionBeginning', function ($ev) {
                \Log::debug("beginTransaction (connection={$ev->connectionName})");
            });
            Event::listen('Illuminate\Database\Events\TransactionCommitted', function ($ev) {
                \Log::debug("commit (connection={$ev->connectionName})");
            });
            Event::listen('Illuminate\Database\Events\TransactionRolledBack', function ($ev) {
                \Log::debug("rollback (connection={$ev->connectionName})");
            });
        }
    }
}
