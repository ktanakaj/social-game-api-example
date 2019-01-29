<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\Admins\Administrator;

class AdministratorControllerTest extends TestCase
{
    /**
     * 認証中の管理者情報のテスト。
     */
    public function testMe() : void
    {
        $response = $this->withAdminLogin()->json('GET', '/admin/administrators/me');
        $response
            ->assertStatus(200)
            ->assertJson([
                'email' => 'admin',
                'role' => 0,
            ]);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('note', $json);
        $this->assertArrayHasKey('created_at', $json);
        $this->assertArrayHasKey('updated_at', $json);
        $this->assertArrayHasKey('deleted_at', $json);
        $this->assertArrayNotHasKey('password', $json);
    }
}
