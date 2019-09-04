<?php

namespace App\Logging;

use Monolog\Formatter\NormalizerFormatter;

/**
 * アクセスログをCombinedLog+αに成形するフォーマッター。
 */
class AccessLogFormatter extends NormalizerFormatter
{
    /** 日付フォーマット */
    const SIMPLE_DATE = "d/M/Y:H:i:s O";

    /**
     * ログをフォーマットする。
     * @param array $record ログ情報。
     * @return string 成形したログ文字列。
     */
    public function format(array $record) : string
    {
        $vars = parent::format($record);
        $context = $vars['context'];
        $req = $context['req'];
        $res = $context['res'];

        // 以下CombinedLog+αの形式で出力。
        // このログは開発者向けのデバッグ用の想定。CombinedLog対応のツールなどでパース出来るとは限らないので注意。
        // （CombinedLog風にしているのは、有名なフォーマットに合わせた方が開発者が理解し易いだろうというだけ。）

        // CombinedLog部分の成形
        $output = "{$req['ip']} - " . (isset($context['userId']) ? $context['userId'] : '-')
            . " [{$vars['datetime']}] \"{$req['method']} {$req['url']}"
            . " {$req['protocol']}\" {$res['status']}"
            . ' ' . (!empty($res['contentLength']) ? $res['contentLength'] : '-')
            . " \"{$req['referrer']}\" \"{$req['userAgent']}\"";

        // +α部分の成形（データがある場合のみ）
        // ※ contentはデータによっては複数行になる
        if (isset($context['times'])) {
            $output .= " {$context['times']}ms";
        }
        if (array_key_exists('content', $req)) {
            $output .= " req={$req['content']}";
        }
        if (array_key_exists('content', $res)) {
            $output .= " res={$res['content']}";
        }

        return $output . "\n";
    }
}
