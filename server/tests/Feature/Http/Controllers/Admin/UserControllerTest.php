<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use App\Models\Globals\User;

class UserControllerTest extends TestCase
{
    /**
     * ユーザー一覧のテスト。
     */
    public function testIndex() : void
    {
        $user = factory(User::class)->create();

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

    /**
     * ユーザー更新のテスト。
     */
    public function testUpdate() : void
    {
        $user = factory(User::class)->create();
        $body = [
            'name' => 'updated by testUpdate()',
            'gameCoins' => 100000,
            'specialCoins' => 100,
            'freeSpecialCoins' => 1000,
            'exp' => 10000,
            'stamina' => 150,
        ];
        $response = $this->withAdminLogin()->json('PUT', "/admin/users/{$user->id}", $body);
        $response
            ->assertStatus(200)
            ->assertJson($body);

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);

        $this->assertDatabaseHas('users', [
            'id' => $json['id'],
            'name' => $json['name'],
            'game_coins' => $json['gameCoins'],
            'special_coins' => $json['specialCoins'],
            'free_special_coins' => $json['freeSpecialCoins'],
            'exp' => $json['exp'],
            'stamina' => $json['stamina'],
        ]);
    }
}
