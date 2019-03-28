<?php

namespace Tests;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Models\Admins\Administrator;
use App\Models\Globals\User;

/**
 * 全テスト共通の処理用の抽象クラス。
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /** @var boolean */
    private static $initialized = false;

    /**
     * 各テストの実行前に呼ばれる処理。
     * ※ 子クラスでオーバーライドする際は、必ず parent::setUp() を実行してください。
     */
    protected function setUp() : void
    {
        // ※ parentのsetup()でLaravelの初期化が行われるので呼び出し忘れずに
        parent::setUp();

        // 初回実行時にDBとRedisを初期化する
        // ※ 他の場所 (ExtensionやsetUpBeforeClass) も検討したが、Laravelが初期化されていなくて面倒なのでここでやる
        if (!self::$initialized) {
            Redis::flushdb();
            $this->migrateDb();
            self::$initialized = true;
        }
    }

    /**
     * DBをマイグレーションする。
     */
    private function migrateDb() : void
    {
        // Sqliteの場合、migrate実行前に空のファイルで上書きする
        // （MySQLでやる場合は、全テーブルDROP等する）
        $connections = config('database.connections');
        foreach ($connections as $name => $conn) {
            $file = $conn['database'];
            if ($conn['driver'] === 'sqlite' && $file !== ':memory:') {
                file_put_contents($file, '');
            }
            Artisan::call('migrate:refresh', [
                '--path' => 'database/migrations/' . str_plural($name),
                '--database' => $name,
            ]);
        }
        Artisan::call('master:import', ['directory' => 'tests/Masters']);
        Artisan::call('db:seed');
    }

    /**
     * 指定されたユーザーで認証済の状態にする。
     * @param User $user ユーザー。未指定時は新規テスト用ユーザー。
     * @return TestCase $this
     */
    protected function withLogin(User $user = null) : TestCase
    {
        if ($user === null) {
            $user = factory(User::class)->create();
        }
        Auth::login($user);
        return $this;
    }

    /**
     * 指定された管理者で認証済の状態にする。
     * @param Administrator $admin 管理者。未指定時は初期管理者。
     * @return TestCase $this
     */
    protected function withAdminLogin(Administrator $admin = null) : TestCase
    {
        if ($admin === null) {
            $admin = Administrator::where('email', 'admin')->firstOrFail();
        }
        Auth::guard('admin')->login($admin);
        return $this;
    }

    /**
     * テスト用に現在時刻を変更する。
     * @param mixed 設定する現在時刻。
     */
    protected function setTestNow($date) : void
    {
        // Carbonとリクエスト時間を更新。
        // またタイムゾーンをアプリのデフォルトに初期化。
        if (is_object($date)) {
            $now = Carbon::instance($date);
        } else {
            $now = new Carbon($date);
        }
        $_SERVER['REQUEST_TIME'] = $now->timestamp;
        Carbon::setTestNow($now->setTimezone(config('app.timezone')));
    }
}
