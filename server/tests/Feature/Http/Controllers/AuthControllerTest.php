<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Globals\User;

class AuthControllerTest extends TestCase
{
    /**
     * ログインのテスト。
     */
    public function testLogin() : void
    {
        $user = factory(User::class)->create([
            'token' => bcrypt('TEST_LOGIN_TOKEN'),
        ]);

        $response = $this->json('POST', '/login', [
            'id' => $user->id,
            'token' => 'TEST_LOGIN_TOKEN',
        ]);
        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayNotHasKey('token', $json);
        $this->assertAuthenticated();
    }

    /**
     * ログイン（認証NG）のテスト。
     */
    public function testLoginAtFailed() : void
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', '/login', [
            'id' => $user->id,
            'token' => 'TEST_LOGIN_TOKEN',
        ]);
        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => [
                    'code' => 'BAD_REQUEST',
                    'message' => 'id or token is incorrect',
                ],
            ]);

        $this->assertGuest();
    }

    /**
     * ログアウトのテスト。
     */
    public function testLogout() : void
    {
        $response = $this->withLogin()->json('POST', '/logout');
        $response->assertStatus(200);
        $this->assertGuest();
    }
}
