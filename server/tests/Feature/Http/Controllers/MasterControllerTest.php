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
        $this->assertInternalType('array', $array);
        $this->assertContains('error_codes', $array);
        $this->assertNotContains('migrations', $array);
    }

    /**
     * マスタ取得のテスト。
     */
    public function testFindMaster() : void
    {
        $response = $this->json('GET', '/masters/error_codes');
        $response->assertStatus(200);

        $array = $response->json();
        $this->assertInternalType('array', $array);
        $this->assertGreaterThan(0, count($array));
        $this->assertEquals(ErrorCode::findOrFail('INTERNAL_SERVER_ERROR')->toArray(), $array[0]);
    }
}
