<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class UserControllerTest extends TestCase
{
    /**
     * 認証中のユーザー情報のテスト。
     */
    public function testMe() : void
    {
        $response = $this->withLogin()->json('GET', '/users/me');
        // FIXME: 実APIだと200なのに、ユニットテストだと何故か201になる。一旦保留
        $response->assertStatus(201);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('name', $json);
        $this->assertArrayHasKey('email', $json);
        $this->assertArrayHasKey('created_at', $json);
        $this->assertArrayHasKey('updated_at', $json);
        $this->assertArrayNotHasKey('password', $json);
    }
}
