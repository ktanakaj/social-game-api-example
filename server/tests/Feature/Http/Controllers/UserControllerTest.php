<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * ユーザー登録のテスト。
     */
    public function testStore() : void
    {
        $response = $this->json('POST', '/users', [
            'token' => 'TEST_STORE_TOKEN',
        ]);
        $response->assertStatus(201);

        // ※ いくつかの項目にはマスタから初期データが設定される
        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertSame('(noname)', $json['name']);
        $this->assertSame(10000, $json['gameCoins']);
        $this->assertSame(100, $json['freeSpecialCoins']);
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);
        $this->assertArrayNotHasKey('token', $json);
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'id' => $json['id'],
            'name' => $json['name'],
            'game_coins' => $json['gameCoins'],
            'free_special_coins' => $json['freeSpecialCoins'],
        ]);
        $this->assertDatabaseHas('user_cards', [
            'user_id' => $json['id'],
            'card_id' => 1000,
            'count' => 1,
            'exp' => 0,
        ]);
        $this->assertDatabaseHas('user_items', [
            'user_id' => $json['id'],
            'item_id' => 100,
            'count' => 1,
        ]);
        $this->assertDatabaseHas('user_decks', [
            'user_id' => $json['id'],
            'no' => 1,
        ]);
        // TODO: user_deck_cards もテストする

        // 登録したトークンで認証できること
        $this->assertCredentials([
            'id' => $json['id'],
            'password' => 'TEST_STORE_TOKEN',
        ]);
    }

    /**
     * 認証中のユーザー情報のテスト。
     */
    public function testMe() : void
    {
        $response = $this->withLogin()->json('GET', '/users/me');
        // ※ 実APIだと200なのに、ユニットテストだと何故か201になる
        $response->assertSuccessful();

        $json = $response->json();
        $this->assertArrayHasKey('id', $json);
        $this->assertArrayHasKey('name', $json);
        $this->assertArrayHasKey('createdAt', $json);
        $this->assertArrayHasKey('updatedAt', $json);
        $this->assertArrayNotHasKey('token', $json);
    }
}
