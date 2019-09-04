<?php

use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'default' => [
            'driver' => 'stack',
            'channels' => ['daily', 'errorlog'],
        ],

        'single' => [
            'driver' => 'single',
            'path' => env('LOG_DEFAULT_PATH', storage_path('logs/laravel.log')),
            'level' => env('LOG_DEFAULT_LEVEL', 'info'),
            'permission' => 0666,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => env('LOG_DEFAULT_PATH', storage_path('logs/laravel.log')),
            'level' => env('LOG_DEFAULT_LEVEL', 'info'),
            'days' => env('LOG_DEFAULT_DAYS', 14),
            'permission' => 0666,
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_DEFAULT_LEVEL', 'info'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'error',
        ],

        'access' => [
            'driver' => 'daily',
            'path' => env('LOG_ACCESS_PATH', storage_path('logs/access.log')),
            'level' => env('LOG_ACCESS_LEVEL', 'info'),
            'days' => env('LOG_ACCESS_DAYS', 14),
            'permission' => 0666,
            'tap' => [App\Logging\AccessLogFormatterTapper::class],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Batch Log
    |--------------------------------------------------------------------------
    |
    | コンソールコマンドのログ出力先パス。
    | 現状、標準出力をリダイレクトしているので、通常のログのような制御は行えない。
    |
    */

    'batchlog' => env('LOG_BATCH_PATH', storage_path('logs/batch.log')),

];
