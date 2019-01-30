<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\Globals\User;

class AuthControllerTest extends TestCase
{
    /**
     * ログインのテスト。
     */
    public function testLogin() : void
    {
        // ユーザーを生成してログイン
        $user = new User();
        $user->token = bcrypt('TEST_LOGIN_TOKEN');
        $user->save();

        $response = $this->json('POST', '/login', [
            'id' => $user->id,
            'token' => 'TEST_LOGIN_TOKEN',
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayNotHasKey('token', $json);
        $this->assertTrue(Auth::check());
    }

    /**
     * ログアウトのテスト。
     */
    public function testLogout() : void
    {
        $response = $this->withLogin()->json('POST', '/logout');
        $response->assertStatus(200);
        $this->assertFalse(Auth::check());
    }
}
