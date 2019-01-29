<?php

namespace Tests\Feature\Exceptions;

use Tests\TestCase;

class HandlerTest extends TestCase
{
    /**
     * エラーハンドラーのテスト。
     */
    public function test() : void
    {
        // APIを叩いてエラーとなった場合のテスト
        // ルート未存在など、他のテストに含めることができないものを実施
        $response = $this->json('GET', '/invalidpath');
        $response
            ->assertStatus(404)
            ->assertExactJson([
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => '',
                ],
            ]);
    }
}
