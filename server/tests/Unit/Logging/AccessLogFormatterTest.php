<?php

namespace Tests\Unit\Logging;

use Tests\TestCase;
use Carbon\Carbon;
use App\Logging\AccessLogFormatter;

class AccessLogFormatterTest extends TestCase
{
    /**
     * ログをフォーマットのテスト。
     */
    public function testFormat() : void
    {
        $formatter = new AccessLogFormatter();

        // 一般的なPOSTログ
        $this->assertSame(
            '172.17.166.65 - 1001 [04/Sep/2019:09:55:32 +0000] "POST http://172.17.166.74/game/start HTTP/1.1" 200 29 "" "Mozilla/5.0 (Windows NT 10.0; Win64; x64)" 1738ms req={"questId":1,"deckId":1} res={"questlogId":3,"stamina":30}' . "\n",
            $formatter->format([
                'datetime' => new Carbon('2019-09-04T09:55:32Z'),
                'message' => '',
                'context' => [
                    'req' => [
                        'ip' => '172.17.166.65',
                        'method' => 'POST',
                        'url' => 'http://172.17.166.74/game/start',
                        'protocol' => 'HTTP/1.1',
                        'referrer' => null,
                        'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                        'content' => '{"questId":1,"deckId":1}',
                    ],
                    'res' => [
                        'status' => 200,
                        'contentLength' => 29,
                        'content' => '{"questlogId":3,"stamina":30}',
                    ],
                    'userId' => 1001,
                    'times' => 1738,
                ],
                'extra' => [],
            ])
        );

        // レスポンスが無い場合のアクセスログ
        $this->assertSame(
            '172.17.166.65 - - [04/Sep/2019:10:12:14 +0000] "GET http://172.17.166.74/admin/users?max=100&page=1 HTTP/1.1" 401 - "" "Mozilla/5.0 (Windows NT 10.0; Win64; x64)" 502ms req= res=' . "\n",
            $formatter->format([
                'datetime' => new Carbon('2019-09-04T10:12:14Z'),
                'message' => '',
                'context' => [
                    'req' => [
                        'ip' => '172.17.166.65',
                        'method' => 'GET',
                        'url' => 'http://172.17.166.74/admin/users?max=100&page=1',
                        'protocol' => 'HTTP/1.1',
                        'referrer' => null,
                        'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                        'content' => null,
                    ],
                    'res' => [
                        'status' => 401,
                        'contentLength' => 0,
                        'content' => null,
                    ],
                    'times' => 502,
                ],
                'extra' => [],
            ])
        );

        // バイナリ画像ダウンロードのアクセスログ
        // コンテンツはあるが中身が取れない。
        $this->assertSame(
            '172.17.166.65 - - [04/Sep/2019:09:28:06 +0000] "GET http://172.17.166.74/images/3.dat?cache=1566367775000 HTTP/1.1" 200 60000 "http://172.17.166.74/images/" "Mozilla/5.0 (Windows NT 10.0; Win64; x64)" 591ms req= res=' . "\n",
            $formatter->format([
                'datetime' => new Carbon('2019-09-04T09:28:06Z'),
                'message' => '',
                'context' => [
                    'req' => [
                        'ip' => '172.17.166.65',
                        'method' => 'GET',
                        'url' => 'http://172.17.166.74/images/3.dat?cache=1566367775000',
                        'protocol' => 'HTTP/1.1',
                        'referrer' => 'http://172.17.166.74/images/',
                        'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                        'content' => null,
                    ],
                    'res' => [
                        'status' => 200,
                        'contentLength' => 60000,
                        'content' => false,
                    ],
                    'times' => 591,
                ],
                'extra' => [],
            ])
        );

        // コンテンツや時間は、未指定時は出力されない
        $this->assertSame(
            '172.17.166.65 - - [04/Sep/2019:10:00:17 +0000] "GET http://172.17.166.74/masters HTTP/1.1" 200 116 "http://172.17.166.74/swagger/?url=/api-docs.json" "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"' . "\n",
            $formatter->format([
                'datetime' => new Carbon('2019-09-04T10:00:17Z'),
                'message' => '',
                'context' => [
                    'req' => [
                        'ip' => '172.17.166.65',
                        'method' => 'GET',
                        'url' => 'http://172.17.166.74/masters',
                        'protocol' => 'HTTP/1.1',
                        'referrer' => 'http://172.17.166.74/swagger/?url=/api-docs.json',
                        'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    ],
                    'res' => [
                        'status' => 200,
                        'contentLength' => 116,
                    ],
                ],
                'extra' => [],
            ])
        );
    }
}
