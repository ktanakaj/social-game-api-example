<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

class OpenApiControllerTest extends TestCase
{
    /**
     * api-docs.json のテスト。
     */
    public function test() : void
    {
        // デバッグ用機能なので正常にレスポンスが返ってくればOK
        $_SERVER['REQUEST_URI'] = 'http://localhost/game-svr/api-docs.json';
        $response = $this->json('GET', '/api-docs.json');
        $response->assertStatus(200);
    }
}
