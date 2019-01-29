<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class AuthControllerTest extends TestCase
{
    /**
     * ログインのテスト。
     */
    public function testLogin() : void
    {
        // 初期管理者でログイン
        $response = $this->json('POST', '/admin/login', [
            'email' => 'admin',
            'password' => 'admin01',
        ]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'email' => 'admin',
                'role' => 0,
            ]);
        $json = $response->json();
        $this->assertArrayNotHasKey('password', $json);
        $this->assertTrue(Auth::guard('admin')->check());
    }

    /**
     * ログアウトのテスト。
     */
    public function testLogout() : void
    {
        $response = $this->withAdminLogin()->json('POST', '/admin/logout');
        $response->assertStatus(200);
        $this->assertFalse(Auth::guard('admin')->check());
    }
}
