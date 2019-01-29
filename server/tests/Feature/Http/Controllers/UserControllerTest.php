<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\Globals\User;

class UserControllerTest extends TestCase
{
    /**
     * ログアウトのテスト。
     */
    public function testLogout() : void
    {
        $response = $this->withLogin()->json('POST', '/users/logout');
        $response->assertStatus(200);
        $this->assertFalse(Auth::check());
    }
}
