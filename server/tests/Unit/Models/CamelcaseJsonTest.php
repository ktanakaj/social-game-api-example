<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use Illuminate\Database\Eloquent\Model;
use App\Models\CamelcaseJson;

class CamelcaseJsonTest extends TestCase
{
    /**
     * 連想配列で渡された値をモデルのプロパティに格納のテスト。
     */
    public function testFill() : void
    {
        // traitなので、ダミーのクラスを作ってテスト
        $mock = new class extends Model {
            use CamelcaseJson;
            protected $guarded = [];
        };

        // 通常パターン
        $mock->fill([
            'key' => 'value',
            'snake_key' => 'snake_value',
            'camelKey' => 'camelValue',
        ]);
        $this->assertSame('value', $mock->key);
        $this->assertSame('snake_value', $mock->snake_key);
        $this->assertSame('camelValue', $mock->camel_key);
        $this->assertNull($mock->camelKey);

        // 不正パターン。スネークケースのキー名があっても、キャメルケースが優先
        $mock->fill([
            'test_key' => 'INVALID_VALUE',
            'testKey' => 'ok',
            'test_key' => 'INVALID_VALUE',
        ]);
        $this->assertSame('ok', $mock->test_key);
        $this->assertNull($mock->testKey);
    }

    /**
     * モデルを連想配列に変換のテスト。
     */
    public function testToArray() : void
    {
        // traitなので、ダミーのクラスを作ってテスト
        $mock = new class extends Model { use CamelcaseJson; };
        $mock->key = 'value';
        $mock->snake_key = 'snake_value';
        $mock->camelKey = 'camelValue';

        $this->assertSame([
            'key' => 'value',
            'snakeKey' => 'snake_value',
            'camelKey' => 'camelValue',
        ], $mock->toArray());
    }
}
