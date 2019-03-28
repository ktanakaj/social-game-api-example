<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Tests\TestCase;
use App\Models\Globals\User;
use App\Models\Globals\Gachalog;

class GachaControllerTest extends TestCase
{
    /**
     * ガチャ履歴のテスト。
     */
    public function testLogs() : void
    {
        $user = factory(User::class)->create();
        $user->gachalogs()->save(factory(Gachalog::class)->make());

        // ページング条件なしで取得
        $response = $this->withAdminLogin()->json('GET', "/admin/users/{$user->id}/gachas/logs");
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

        $log = $json['data'][0];
        $this->assertArrayHasKey('id', $log);
        $this->assertArrayHasKey('userId', $log);
        $this->assertArrayHasKey('gachaId', $log);
        $this->assertArrayHasKey('gachaPriceId', $log);
        $this->assertArrayHasKey('createdAt', $log);

        // TODO: 未実装
    }
}
