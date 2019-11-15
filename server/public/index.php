<?php

// Xhprofでの分析用コード。
// php.ini で tideways_xhprof を有効にすると、実行されるようになります。
if (function_exists('tideways_xhprof_enable')) {
    tideways_xhprof_enable(TIDEWAYS_XHPROF_FLAGS_MEMORY | TIDEWAYS_XHPROF_FLAGS_CPU);
    register_shutdown_function(function () {
        // ※ 出力先のディレクトリは事前に手動で作成しておいてください
        $filename = '/var/xhprof/' . uniqid() . '.vision-server';
        if (!empty($_SERVER['REQUEST_URI'])) {
            $filename .= preg_replace('/\?.*$/', '', str_replace('/', '_', $_SERVER['REQUEST_URI']));
        }
        $filename .= '_' . date('YmdHis') . '.xhprof';
        if (file_put_contents($filename, serialize(tideways_xhprof_disable())) === false) {
            error_log("{$filename} output failed");
        }
    });
}

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
