<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot() : void
    {
        \Route::pattern('id', '[0-9]+');
        \Route::pattern('userItemId', '[0-9]+');
        \Route::pattern('userCardId', '[0-9]+');
        \Route::pattern('userGiftId', '[0-9]+');
        \Route::pattern('userDeckId', '[0-9]+');
        \Route::pattern('userAchievementId', '[0-9]+');
        \Route::pattern('gachaId', '[0-9]+');

        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map() : void
    {
        $this->mapApiRoutes();
        $this->mapAdminRoutes();
    }

    /**
     * API用ルートの定義。
     */
    protected function mapApiRoutes() : void
    {
        \Route::middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }

    /**
     * 管理画面API用ルートの定義。
     */
    protected function mapAdminRoutes() : void
    {
        \Route::prefix('admin')
             ->middleware('admin')
             ->namespace($this->namespace . '\Admin')
             ->group(base_path('routes/admin.php'));
    }
}
