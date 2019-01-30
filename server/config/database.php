<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'global'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'global' => [
            'driver' => env('DB_GLOBAL_DRIVER', 'mysql'),
            'host' => env('DB_GLOBAL_HOST', '127.0.0.1'),
            'port' => env('DB_GLOBAL_PORT', '3306'),
            'database' => env('DB_GLOBAL_DATABASE', 'game_global_db'),
            'username' => env('DB_GLOBAL_USERNAME', 'game_usr'),
            'password' => env('DB_GLOBAL_PASSWORD', 'game001'),
            'unix_socket' => env('DB_GLOBAL_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'master' => [
            'driver' => env('DB_MASTER_DRIVER', 'mysql'),
            'host' => env('DB_MASTER_HOST', '127.0.0.1'),
            'port' => env('DB_MASTER_PORT', '3306'),
            'database' => env('DB_MASTER_DATABASE', 'game_master_db'),
            'username' => env('DB_MASTER_USERNAME', 'game_usr'),
            'password' => env('DB_MASTER_PASSWORD', 'game001'),
            'unix_socket' => env('DB_MASTER_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'admin' => [
            'driver' => env('DB_ADMIN_DRIVER', 'mysql'),
            'host' => env('DB_ADMIN_HOST', '127.0.0.1'),
            'port' => env('DB_ADMIN_PORT', '3306'),
            'database' => env('DB_ADMIN_DATABASE', 'game_admin_db'),
            'username' => env('DB_ADMIN_USERNAME', 'game_usr'),
            'password' => env('DB_ADMIN_PASSWORD', 'game001'),
            'unix_socket' => env('DB_ADMIN_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_DEFAULT_HOST', '127.0.0.1'),
            'password' => env('REDIS_DEFAULT_PASSWORD', null),
            'port' => env('REDIS_DEFAULT_PORT', 6379),
            'database' => env('REDIS_DEFAULT_DB', 1),
        ],

        'cache' => [
            'host' => env('REDIS_CACHE_HOST', '127.0.0.1'),
            'password' => env('REDIS_CACHE_PASSWORD', null),
            'port' => env('REDIS_CACHE_PORT', 6379),
            'database' => env('REDIS_CACHE_DB', 2),
        ],

        'session' => [
            'host' => env('REDIS_SESSION_HOST', '127.0.0.1'),
            'password' => env('REDIS_SESSION_PASSWORD', null),
            'port' => env('REDIS_SESSION_PORT', 6379),
            'database' => env('REDIS_SESSION_DB', 3),
        ],

    ],

];
