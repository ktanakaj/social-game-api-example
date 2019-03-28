<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Enums\AdminRole;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot() : void
    {
        $this->registerPolicies();

        // 管理画面APIのロール判定用ゲート
        \Gate::define('admin', function ($admin) {
            return isset($admin->role) && $admin->role === AdminRole::ADMIN;
        });
        \Gate::define('writable', function ($admin) {
            return isset($admin->role) && in_array($admin->role, [AdminRole::ADMIN, AdminRole::WRITABLE]);
        });
    }
}
