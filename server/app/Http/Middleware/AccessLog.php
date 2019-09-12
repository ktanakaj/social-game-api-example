<?php

namespace App\Http\Middleware;

use Illuminate\Http\JsonResponse;

/**
 * アクセスログを出力するミドルウェア。
 */
class AccessLog
{
    /**
     * リクエストハンドラー。
     * @param \Illuminate\Http\Request $request リクエスト。
     * @param \Closure $next 次のリクエストハンドラー。
     * @return mixed レスポンス。
     */
    public function handle($request, \Closure $next)
    {
        // アクセス開始時の時間を記録して処理実行、ログ出力
        $starttime = microtime(true);
        $response = $next($request);
        self::log($request, $response, $starttime);
        return $response;
    }

    /**
     * アクセスログを出力する。
     * @param \Illuminate\Http\Request $req HTTPリクエスト。
     * @param \Symfony\Component\HttpFoundation\Response $res HTTPレスポンス。
     * @param float $starttime 処理時間測定用の開始時のmicrotime。
     */
    private static function log($req, $res, float $starttime = null) : void
    {
        try {
            // ステータスコードによってログレベルを切り替え
            $level = 'info';
            if ($res->isClientError()) {
                $level = 'warning';
            } elseif ($res->isServerError()) {
                $level = 'error';
            }

            // アクセスログの成形はフォーマッターが行うため、以下ここでは出力データの収集のみを行う
            $context = [
                'req' => [
                    'ip' => $req->ip(),
                    'method' => $req->method(),
                    'url' => $req->fullUrl(),
                    'protocol' => $req->server->get('SERVER_PROTOCOL'),
                    'referrer' => $req->server->get('HTTP_REFERER'),
                    'userAgent' => $req->userAgent(),
                ],
                'res' => [
                    'status' => $res->getStatusCode(),
                ],
            ];

            // 処理時間
            if ($starttime !== null) {
                $context['times'] = round((microtime(true) - $starttime) * 1000);
            }

            // ユーザーID/管理者ID
            // ※ どちらが入っているかはURLから分かるので現状区別しない
            $user = $req->user();
            if ($user) {
                $context['userId'] = $user->id;
            }

            // リクエストボディ/レスポンスボディ
            // ※ StreamedResponseなどのcontentが取れないレスポンスは現状空扱い。contentLengthも0になる。
            $resBody = $res->getContent();
            $context['res']['contentLength'] = strlen($resBody);
            if (config('app.debug')) {
                $reqBody = $req->getContent();
                if ($req->isJson()) {
                    $reqBody = self::hidePasswordLog($reqBody);
                }
                if ($res instanceof JsonResponse) {
                    $resBody = self::hidePasswordLog($resBody);
                }
                $context['req']['content'] = $reqBody;
                $context['res']['content'] = $resBody;
            }

            \Log::channel('access')->{$level}('', $context);
        } catch (\Throwable $ex) {
            \Log::error($ex);
        }
    }

    /**
     * JSONログ上のパスワードを隠す。
     * @param string $log JSONログ文字列。
     * @return string パスワードを置換したログ文字列。
     */
    private static function hidePasswordLog(string $log) : string
    {
        // TODO: パスワードに"が含まれていると中途半端になるかも
        if ($log === null) {
            return $log;
        }
        return preg_replace('/("password"\s*:\s*)".*?"/i', '$1"****"', $log);
    }
}
