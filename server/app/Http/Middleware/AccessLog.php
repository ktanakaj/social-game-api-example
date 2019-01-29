<?php

namespace App\Http\Middleware;

use Closure;
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
    public function handle($request, Closure $next)
    {
        // アクセス開始時の時間を記録して処理実行、ログ出力
        $starttime = microtime(true);
        $response = $next($request);
        self::log($request, $response, $starttime);
        return $response;
    }

    /**
     * アクセスログを出力する。
     * @param Request $req HTTPリクエスト。
     * @param Response|JsonResponse|RedirectResponse $res HTTPレスポンス。
     * @param float $starttime 処理時間測定用の開始時のmicrotime。
     */
    private static function log($req, $res, float $starttime = null) : void
    {
        try {
            // ステータスコードによってログレベルを切り替え
            $level = 'info';
            if ($res->isClientError()) {
                $level = 'warn';
            } elseif ($res->isServerError()) {
                $level = 'error';
            }

            // ※ 以下、CombinedLogに+αのデバッグ情報を載せて出力しています。
            //    現状のフォーマットは適当にデバッグ用途メインに決めたものなので、
            //    KPIなどにアクセスログが欲しい場合は、丸々置き換えてしまって構いません。

            // 基本のアクセスログ
            $resBody = $res->content();
            $contentLength = strlen($resBody);
            $log = "{$req->ip()} - - \"{$req->method()} {$req->fullUrl()}"
                . " {$req->server->get('SERVER_PROTOCOL')}\" {$res->status()}"
                . " {$contentLength} \"{$req->server->get('HTTP_REFERER')}\""
                . " \"{$req->userAgent()}\"";

            // 処理時間
            if ($starttime !== null) {
                $log .= ' ' . round((microtime(true) - $starttime) * 1000) . 'ms';
            }

            // ユーザーID/管理者ID
            // ※ どちらが入っているかはURLから分かるので現状区別しない
            $user = $req->user();
            if ($user) {
                $log .= ' id=' . $user->id;
            }

            // リクエストボディ/レスポンスボディ
            if (config('app.debug')) {
                $reqBody = $req->getContent();
                if ($req->isJson()) {
                    $reqBody = self::hidePasswordLog($reqBody);
                }
                if ($res instanceof JsonResponse) {
                    $resBody = self::hidePasswordLog($resBody);
                }
                $log .= " req={$reqBody} res={$resBody}";
            }

            \Log::channel('access')->{$level}($log);
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
