<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * ユーザー一覧のテスト。
     */
    public function testIndex() : void
    {
        // 一人ユーザーを作成
        $this->createTestUser();

        // ページング条件なしで取得
        $response = $this->withAdminLogin()->json('GET', '/admin/users');
        $response
            ->assertStatus(200)
            ->assertJson([
                'perPage' => 100,
                'currentPage' => 1,
                'from' => 1,
            ]);

        $json = $response->json();
        $this->assertGreaterThan(0, $json['total']);
        $this->assertGreaterThan(0, $json['lastPage']);
        $this->assertGreaterThan(0, $json['to']);
        $this->assertGreaterThan(0, count($json['data']));

        $user = $json['data'][0];
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('name', $user);
        $this->assertArrayHasKey('gameCoins', $user);
        $this->assertArrayHasKey('specialCoins', $user);
        $this->assertArrayHasKey('freeSpecialCoins', $user);
        $this->assertArrayHasKey('exp', $user);
        $this->assertArrayHasKey('stamina', $user);
        $this->assertArrayHasKey('lastLogin', $user);
        $this->assertArrayHasKey('createdAt', $user);
        $this->assertArrayHasKey('updatedAt', $user);
        $this->assertArrayNotHasKey('token', $user);
        $this->assertArrayNotHasKey('staminaUpdatedAt', $user);
    }
}
