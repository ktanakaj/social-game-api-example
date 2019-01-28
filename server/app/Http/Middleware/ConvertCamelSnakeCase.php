<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

/**
 * リクエスト/レスポンスのキャメルケース/スネークケース変換を行うミドルウェア。
 */
class ConvertCamelSnakeCase
{
    /**
     * リクエストハンドラー。
     * @param \Illuminate\Http\Request $request リクエスト。
     * @param \Closure $next 次のリクエストハンドラー。
     * @return mixed レスポンス。
     */
    public function handle($request, Closure $next)
    {
        // ※ 主にモデルの列名をキャメルケースに変換することを想定。
        //    そのため、現状レスポンスのみ変換。
        $response = $next($request);
        if ($response instanceof JsonResponse) {
            // stdClassのキーをキャメルケースに変換する
            $response->setData(self::camelizeKeysRecursive($response->getData()));
        }
        return $response;
    }

    /**
     * オブジェクトのキーを再帰的にキャメルケースに変換する。
     * @param mixed $obj 変換元のオブジェクト。
     * @return mixed 変換したオブジェクト。
     */
    private static function camelizeKeysRecursive($obj)
    {
        // nullや数値、文字列などはそのまま返す
        if ($obj === null || is_scalar($obj)) {
            return $obj;
        }
        // 配列は中身を再帰的に処理する
        // ※ ここでは連想配列は処理対象外
        if (is_array($obj)) {
            return array_map('self::camelizeKeysRecursive', $obj);
        }
        // オブジェクトはキーを全て変換（全て大文字などもキャメルケースにする）
        // ※ ここではstdClass以外のオブジェクトも処理対象外
        $result = new \stdClass();
        foreach (get_object_vars($obj) as $key => $value) {
            $result->{camel_case($key)} = self::camelizeKeysRecursive($value);
        }
        return $result;
    }
}
