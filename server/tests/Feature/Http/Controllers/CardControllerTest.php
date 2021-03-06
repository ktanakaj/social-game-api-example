<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Globals\User;

class CardControllerTest extends TestCase
{
    /**
     * カード一覧のテスト。
     */
    public function testIndex() : void
    {
        $user = factory(User::class)->states('allcards')->create();

        // ページング条件なしで取得
        $response = $this->withLogin($user)->json('GET', '/cards');
        $response
            ->assertStatus(200)
            ->assertJson([
                'perPage' => 20,
                'currentPage' => 1,
                'from' => 1,
            ]);

        $json = $response->json();
        $this->assertGreaterThan(0, $json['total']);
        $this->assertGreaterThan(0, $json['lastPage']);
        $this->assertGreaterThan(0, $json['to']);
        $this->assertGreaterThan(0, count($json['data']));

        $userCard = $json['data'][0];
        $this->assertArrayHasKey('id', $userCard);
        $this->assertArrayHasKey('cardId', $userCard);
        $this->assertArrayHasKey('count', $userCard);
        $this->assertArrayHasKey('exp', $userCard);
        $this->assertArrayHasKey('createdAt', $userCard);
        $this->assertArrayHasKey('updatedAt', $userCard);
    }
}
