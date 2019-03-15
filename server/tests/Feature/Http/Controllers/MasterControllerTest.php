<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Masters\ErrorCode;

class MasterControllerTest extends TestCase
{
    /**
     * マスタ一覧のテスト。
     */
    public function testIndex() : void
    {
        $response = $this->json('GET', '/masters');
        $response->assertStatus(200);

        $array = $response->json();
        $this->assertIsArray($array);
        $this->assertContains('ErrorCode', $array);
        $this->assertNotContains('MasterModel', $array);
    }

    /**
     * マスタ取得のテスト。
     */
    public function testFindMaster() : void
    {
        // 通常のマスタ
        $response = $this->json('GET', '/masters/error_codes');
        $response->assertStatus(200);

        $array = $response->json();
        $this->assertIsArray($array);
        $this->assertGreaterThan(0, count($array));
        $this->assertEquals(ErrorCode::findOrFail('BAD_REQUEST')->toArray(), $array[0]);

        // activeスコープを持つマスタ
        // TODO: 公開中のみ返ることもテストする
        $response = $this->json('GET', '/masters/quests');
        $response->assertStatus(200);
    }
}
