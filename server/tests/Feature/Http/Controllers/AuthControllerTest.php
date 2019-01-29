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
        $user = new User(['name' => 'testLogin', 'email' => 'testLogin@example.com']);
        $user->password = bcrypt('passwd');
        $user->save();

        $response = $this->json('POST', '/login', [
            'email' => $user->email,
            'password' => 'passwd',
        ]);
        $response
            ->assertStatus(200)
            ->assertJson($user->toArray());

        $json = $response->json();
        $this->assertArrayNotHasKey('password', $json);
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
