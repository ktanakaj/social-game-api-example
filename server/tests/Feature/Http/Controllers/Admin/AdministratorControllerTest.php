<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;

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
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);
        $this->assertArrayHasKey('deletedAt', $json);
        $this->assertArrayNotHasKey('password', $json);
    }
}
