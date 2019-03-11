<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Models\Masters\ErrorCode;

/**
 * エラーハンドラー。
 */
class Handler extends ExceptionHandler
{
    /**
     * エラーログに記録しない例外クラス。
     * @var array
     */
    protected $dontReport = [
        // ※ マスタで制御しているため未使用
    ];

    /**
     * バリデーション失敗時にエラー情報として保持しない入力項目。
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * 例外をログに出力する。
     * @param \Exception $exception 発生した例外。
     */
    public function report(Exception $exception) : void
    {
        // エラーを種類に応じて変換、マスタからログレベルを取得してログ出力する
        // ※ ログにはオリジナルの例外を出力する
        try {
            $appex = AppException::convert($exception);
            $errorCode = $this->findByErrorCode($appex->getCode());

            $loglevel = $errorCode->log_level;
            if (!is_callable('\\Log::' . $loglevel)) {
                $loglevel = 'error';
            }

            \Log::{$loglevel}($exception);
        } catch (\Throwable $ex2) {
            error_log($ex2);
        }
    }

    /**
     * 例外をHTTPレスポンスに出力する。
     * @param \Illuminate\Http\Request $request リクエスト。
     * @param \Exception $exception 発生した例外。
     * @return \Illuminate\Http\Response レスポンス。
     */
    public function render($request, Exception $exception)
    {
        // エラーは種類に応じて変換し、マスタから対応するステータスコードやメッセージを取得して処理する
        $appex = AppException::convert($exception);
        $errorCode = $this->findByErrorCode($appex->getCode());
        $status = $errorCode->response_code ?? 500;
        // ※ 本番環境等ではエラーの詳細は返さない
        $msg = config('app.debug') ? $appex->getMessage() : $errorCode->message;

        if ($request->expectsJson()) {
            // JSONの場合、規定のフォーマットで返す
            $info = [
                'code' => $appex->getCode(),
                'message' => $msg,
            ];
            if ($appex->getData() !== null) {
                $info['data'] = $appex->getData();
            }
            return response()->json(['error' => $info], $status);
        } elseif (config('app.debug')) {
            // デバッグモードでのWebからのアクセスの場合、開発用に生の例外を出力する
            if ($exception instanceof ValidationException) {
                // バリデーション例外の場合、LaravelのWebページ用の処理でリダイレクトされてしまうので、変換する
                $exception = new BadRequestHttpException($msg, $exception);
            } elseif ($exception instanceof AuthenticationException) {
                // 認証NGの場合も、LaravelのWebページ用の処理でリダイレクトされてしまうので、変換する
                $exception = new UnauthorizedHttpException('', $msg, $exception);
            } elseif (empty($msg) && $exception instanceof HttpException) {
                // ルート未存在の場合など、エラーメッセージが空で紛らわしいので、詰めなおす
                $exception = new HttpException($status, $errorCode->message, $exception, []);
            }
            return parent::render($request, $exception);
        } else {
            // 通常のWebからのアクセスの場合、AppExceptionのステータスコードからエラーページを出力する
            return parent::render($request, new HttpException($status, $msg, $appex, []));
        }
    }

    /**
     * エラーコードマスタを取得する。
     * @param string $code エラーコード。
     * @return ErrorCode エラーコードマスタ。存在しない場合デフォルト値を返す。
     */
    private function findByErrorCode(string $code) : ErrorCode
    {
        $errorCode = null;
        try {
            $errorCode = ErrorCode::find($code);
        } catch (\Throwable $ex) {
            \Log::error($ex);
        }
        if (!$errorCode) {
            $errorCode = new ErrorCode();
            $errorCode->id = $code;
            $errorCode->message = 'Unknown Error';
            $errorCode->response_code = 500;
            $errorCode->log_level = 'error';
        }
        return $errorCode;
    }
}
