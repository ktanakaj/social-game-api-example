<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Enums\ObjectType;
use App\Models\Globals\User;
use App\Models\Globals\UserItem;

class ItemControllerTest extends TestCase
{
    /**
     * アイテム一覧のテスト。
     */
    public function testIndex() : void
    {
        $user = factory(User::class)->states('allitems')->create();

        // ページング条件なしで取得
        $response = $this->withLogin($user)->json('GET', '/items');
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

        $userItem = $json['data'][0];
        $this->assertArrayHasKey('id', $userItem);
        $this->assertArrayHasKey('itemId', $userItem);
        $this->assertArrayHasKey('count', $userItem);
        $this->assertArrayHasKey('createdAt', $userItem);
        $this->assertArrayHasKey('updatedAt', $userItem);

        // TODO: 0個のアイテムが参照されないこともテストする
    }

    /**
     * アイテム使用のテスト。
     */
    public function testUse() : void
    {
        // ユーザーはスタミナ回復薬を持っている想定なので、それを使う
        $user = factory(User::class)->create();
        $userItem = $user->items[0];

        $response = $this->withLogin($user)->json('POST', "/items/{$userItem->id}/use");
        $response
            ->assertStatus(200)
            ->assertJson([
                [
                    'type' => ObjectType::STAMINA,
                    'id' => null,
                    'count' => 50,
                    'total' => $user->stamina + 50,
                    'isNew' => false,
                ],
            ]);

        $this->assertDatabaseHas('user_items', [
            'id' => $userItem->id,
            'count' => $userItem->count - 1,
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'stamina' => $user->stamina + 50,
        ]);
    }

    /**
     * アイテム使用（0個）のテスト。
     */
    public function testUseWhenEmptyItem() : void
    {
        // ユーザーのスタミナ回復薬を0個にしてテスト
        $user = factory(User::class)->create();
        $userItem = $user->items[0];
        $userItem->count = 0;
        $userItem->save();

        $response = $this->withLogin($user)->json('POST', "/items/{$userItem->id}/use");
        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => [
                    'code' => 'EMPTY_RESOURCE',
                    'message' => 'count = -1 is invalid',
                    'data' => [
                        'type' => ObjectType::ITEM,
                        'count' => 1,
                    ],
                ],
            ]);
    }

    /**
     * アイテム使用（消費系以外を使用）のテスト。
     */
    public function testUseAtInvalidItemType() : void
    {
        // ユーザーの強化素材を使おうとする
        $user = factory(User::class)->create();
        $userItem = factory(UserItem::class)->states('material')->make();
        $user->items()->save($userItem);

        $response = $this->withLogin($user)->json('POST', "/items/{$userItem->id}/use");
        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => [
                    'code' => 'BAD_REQUEST',
                    'message' => "id={$userItem->id} is not usable",
                ],
            ]);
    }
}
